<?php

namespace Kalephan\LKS;

trait EntityForm
{

    protected $entity;

    protected $structure;

    public function formCreate($form)
    {
        $this->formCreateUpdate($form);
        $form->message = lks_lang('Dữ liệu của bạn đã được lưu thành công.');
    }

    public function formUpdate($form)
    {
        $this->formCreateUpdate($form);
        $form->message = lks_lang('Dữ liệu của bạn đã được cập nhật thành công.');
        
        // Add ID field.
        $form->fields[$this->structure->id] = [
            '#name' => $this->structure->id,
            '#type' => 'hidden',
            '#disabled' => true
        ];
    }

    public function formCreateUpdate($form)
    {
        $fields = $this->structure->fields;
        foreach ($fields as $key => $value) {
            if (empty($value['#type'])) {
                continue;
            }
            
            if (isset($value['#default'])) {
                $fields[$key]['#value'] = $value['#default'];
            }
            
            if ($value['#type'] == 'file') {
                $form->form['files'] = true;
                
                if ($value['#widget'] == 'image' && ! isset($value['#validate'])) {
                    $fields[$key]['#validate'] = config('lks.file_image_rule', 'image|mimes:jpeg,png');
                }
            }
        }
        
        $fields['_entity'] = array(
            '#name' => '_entity',
            '#type' => 'hidden',
            '#value' => $this->structure->class,
            '#disabled' => true
        );
        
        $form->fields += $fields;
        $form->actions['submit']['#value'] = lks_lang('Lưu');
        $form->submit[] = get_called_class() . '@formCreateUpdateSubmit';
    }

    public function formCreateUpdateSubmit($form, &$form_values)
    {
        $entity = new \stdClass();
        
        foreach ($this->structure->fields as $key => $value) {
            if (isset($form_values[$key])) {
                if (isset($value['#type'])) {
                    $item = new \stdClass();
                    $item->value = $form_values[$key];
                    event('lks.entityFormCreateUpdateSubmit.' . $value['#type'], $item, $value);
                    $form_values[$key] = $item->value;
                }
                
                $entity->{$key} = $form_values[$key];
            } elseif (isset($value['#default']) && 
            // Don't set default value for update
            ! isset($form_values[$this->structure->id])) {
                $entity->{$key} = $value['#default'];
            }
        }
        
        $form_values[$this->structure->id] = $this->entity->saveEntity($entity);
    }
    
    /*
     * public function loadOptionsAll() {
     * $entities = $this->loadEntityAll();
     *
     * $result = [];
     * foreach ($entities as $value) {
     * $value = $this->loadEntity($value->{$this->structure->id});
     *
     * $result[$value->{$this->structure->id}] = isset($value->title) ? $value->title : $value->{$this->structure->id};
     * }
     *
     * return $result;
     * }
     *
     * public function showRead($lks, $entity_id) {
     * $entity = $this->loadEntity($entity_id, [], true);
     * if (!$entity) {
     * App::abort(404);
     * }
     *
     * if (!empty($entity->title)) {
     * $lks->response->addTitle($entity->title);
     * }
     *
     * $this->showReadExecutive($lks, $entity);
     * }
     *
     * public function showList($lks) {
     * // Load from DB with pagination
     * $pager_items_per_page = config('lks.pagination items per page', 20);
     * $pager_page = intval($lks->request->query('page'));
     * $pager_from = $pager_page > 0 ? ($pager_page - 1) : 0;
     * $pager_from = $pager_from * $pager_items_per_page;
     * $attributes = array(
     * 'pagination' => array(
     * 'start' => $pager_from,
     * 'length' => $pager_items_per_page,
     * ),
     * );
     * $pager_total = 0;
     * $entities = EntityModel::loadEntityAll($this->structure, $attributes, $pager_total);
     *
     * // Build data table
     * $data = array(
     * 'header' => [],
     * 'rows' => [],
     * );
     * //kd($this->structure->fields);
     * foreach ($this->structure->fields as $key => $value) {
     * if (empty($value['#list_hidden'])) {
     * $data['header'][] = ['data' => $value['#title']];
     * }
     * }
     *
     * // Add Operations column
     * $data['header'][] = ['data' => lks_lang('Hoạt động')];
     *
     * if (count($entities)) {
     * foreach ($entities as $entity) {
     * $row = ['data' => []];
     * $entity = $this->loadEntity($entity->{$this->structure->id});
     * foreach ($entity as $key => $value) {
     * if (empty($this->structure->fields[$key]['#list_hidden'])) {
     * switch ($key) {
     * case 'active':
     * if (!empty($this->structure->fields['active']['#options'][$entity->active])) {
     * $value = $this->structure->fields['active']['#options'][$entity->active];
     * }
     * break;
     *
     * case 'created_by':
     * case 'updated_by':
     * $user = lks_instance_get()->load('\Kalephan\User\UserEntity');
     * $user = $user->loadEntity($value);
     *
     * $user_view_url = '';
     * $value = $user_view_url ? lks_anchor($user_view_url, $user->username) : $user->username;
     *
     * break;
     * }
     *
     * $row['data'][] = ['data' => $value];
     * }
     * }
     *
     * //Operations column
     * $row['data'][] = ['data' => implode(', ', lks_entity_contextual_link_get($entity, $this->structure))];
     *
     * $data['rows'][] = $row;
     * }
     * }
     *
     * // Return to browser
     * $vars = array(
     * 'data' => $data,
     * 'add_new' => !empty($this->structure['#action_links']['create']) ? $this->structure['#action_links']['create'] : '',
     * 'pager_items_per_page' => $pager_items_per_page,
     * 'pager_page' => $pager_page,
     * 'pager_total' => $pager_total,
     * 'pager_from' => min($pager_from + 1, $pager_total),
     * 'pager_to' => min($pager_total, $pager_from + $pager_items_per_page),
     * );
     * $template = 'entity_list-' . $this->structure->table;
     * if (!View::exists($template)) {
     * $template = 'entity_list';
     * }
     * return $lks->response->addContent(lks_render($template, $vars));
     * }
     */
    
    // public function showUpdate($lks, $entity_id) {
    /*
     * $entity = $this->loadEntity($entity_id);
     * if (!$entity) {
     * App::abort(404);
     * }
     */
    
    // COMMENTED
    /*
     * $entity_approve = lks_instance_get()->load('\Kalephan\LKS\Approve\ApproveEntity');
     * if (!empty($this->structure['#approve'])
     * && !empty($entity->approve)
     * && $approve = $entity_approve->loadEntity($entity->approve)
     * ) {
     * $entity = $approve;
     * $lks->response->addMessage(lks_lang('":entity_title" đang chờ phê duyệt của quản trị viên. Các dữ liệu cũ vẫn còn được sử dụng để hiển thị. <br />Sau khi phê duyệt thành công, các dữ liệu mới sẽ được chính thức cập nhật.', [':entity_title' => $this->structure['#title']]), 'warning');
     * } elseif (isset($entity->active)
     * && $entity->active != 1
     * ) {
     * $lks->response->addMessage(lks_lang('":entity_title" đang chờ phê duyệt của quản trị viên. Bạn có thể cập nhật nội dung mới, nhưng ":entity_title" sẽ không được hiển thị trên trang web.', [':entity_title' => $this->structure['#title']]), 'warning');
     * }
     */
    
    /*
     * $form_values = lks_object_to_array($entity);
     *
     * $lks->response->addContent(Form::build($this->structure->class . '@showUpdateForm', $form_values));
     */
    // }
    
    /*
     * public function showClone($lks, $entity_id) {
     * $form_values = $this->loadEntity($entity_id);
     * if (!$form_values) {
     * App::abort(404);
     * }
     *
     * $lks->response->addContent(Form::build($this->structure->class . '@showCloneForm', $form_values));
     * }
     *
     * public function showCloneForm() {
     * $form = $this->_showCreateForm();
     * unset($form[$this->structure->id]);
     *
     * return $form;
     * }
     *
     * public function showUpdateForm() {
     * return $this->_showCreateForm();
     * }
     *
     * public function showPreview($lks, $entity_id) {
     * $entity = $this->loadEntity($entity_id, array('cache' => false));
     * if (!$entity) {
     * App::abort(404);
     * }
     *
     * $this->showReadExecutive($lks, $entity);
     * }
     *
     * public function showReadExecutive($lks, $entity) {
     * $lks = lks_instance_get();
     *
     * $event = array(
     * 'entity' => &$entity,
     * 'structure' => $this->structure,
     * );
     * event('entity.showReadExecutive', $event);
     * $entity = $event['entity'];
     *
     * $data = [];
     * $data['entity'] = $entity;
     * foreach ($this->structure->fields as $key => $val) {
     * if (empty($val['#display_hidden'])) {
     * if (isset($val['#options_callback'])) {
     * $val['#options_callback']['arguments'] = isset($val['#options_callback']['arguments']) ? $val['#options_callback']['arguments'] : [];
     * $val['#options_callback']['class'] = explode('@', $val['#options_callback']['class']);
     * $val['#options'] = call_user_func_array(array($lks->load($val['#options_callback']['class'][0]), $val['#options_callback']['class'][1]), $val['#options_callback']['arguments']);
     * }
     *
     * if (isset($val['#options'])) {
     * $entity->$key = (array) $entity->$key;
     * foreach ($entity->$key as $k => $v) {
     * if (isset($val['#options'][$v])) {
     * $entity->{$key}[$k] = $val['#options'][$v];
     * }
     * }
     * $entity->$key = implode(', ', $entity->$key);
     * }
     *
     * switch ($key) {
     * case 'created_by':
     * case 'updated_by':
     * $user = $lks->load('\Kalephan\User\UserEntity');
     * $user = $user->loadEntity($entity->$key);
     * if (!empty($user->username)) {
     * $entity->$key = $user->username;
     * }
     * break;
     * }
     *
     * if (isset($val['#type'])) {
     * $event = array(
     * 'value' => &$entity->$key,
     * 'field' => &$val,
     * );
     * event('entity.showReadExecutive.' . $val['#type'], $event);
     * $entity->$key = $event['value'];
     * $val = $event['field'];
     * }
     *
     * $data['element'][$key] = array(
     * 'title' => isset($val['#title']) ? $val['#title'] : '',
     * 'value' => $entity->$key,
     * );
     * }
     * }
     *
     * $template = 'entity_read-' . $this->structure->table;
     * if (!View::exists($template)) {
     * $template = 'entity_read';
     * }
     * $lks->response->addContent(lks_render($template, $data));
     * }
     *
     * // Create & Update
     * public function showDelete($lks, $entity_id) {
     * $entity = $this->loadEntity($entity_id, array('cache' => false));
     * if (!$entity) {
     * App::abort(404);
     * }
     *
     * $title = isset($entity->title) ? $entity->title : $entity_id;
     *
     * $form_values['notice'] = lks_lang('Bạn có thực sự muốn xóa: :title?', [':title' => $title]);
     * $form_values['entity_id'] = $entity_id;
     *
     * $lks->response->addContent(Form::build($this->structure->class . '@showDeleteForm', $form_values));
     * }
     *
     * public function showDeleteForm() {
     * $form = [];
     *
     * $form['notice'] = array(
     * '#name' => 'notice',
     * '#type' => 'markup',
     * );
     *
     * $form['entity_id'] = array(
     * '#name' => 'entity_id',
     * '#type' => 'hidden',
     * '#disabled' => true,
     * );
     *
     * $form->actions['submit'] = array(
     * '#name' => 'submit',
     * '#type' => 'submit',
     * '#value' => lks_lang('OK'),
     * );
     *
     * $form->actions['cancel'] = array(
     * '#name' => 'cancel',
     * '#type' => 'markup',
     * '#value' => '<a href="javascript:history.back()">' . lks_lang('Hủy') . '</a>',
     * );
     *
     * $form->submit[] = $this->structure->class . '@showDeleteFormSubmit';
     * $form->message = lks_lang('Dữ liệu của bạn đã được xóa thành công.');
     * if (!empty($this->structure['#action_links']['list'])) {
     * $form['#redirect'] = lks_url($this->structure['#action_links']['list']);
     * }
     *
     * return $form;
     * }
     *
     * public function showDeleteFormSubmit($form_id, &$form, &$form_values) {
     * $this->deleteEntity($form_values['entity_id']);
     * }
     *
     * public function showEmptyField($lks, $entity_id, $field) {
     * $entity = $this->loadEntity($entity_id);
     *
     * if (isset($entity->{$field})) {
     * $entity->{$field} = '';
     * $this->saveEntity($entity);
     * }
     * }
     *
     * public function showActive($lks, $entity_id) {
     * $entity = $this->loadEntity($entity_id);
     *
     * $entity->active = 1;
     * $this->saveEntity($entity, true);
     *
     * $entity->title = !empty($entity->title) ? $entity->title : $entity->{$this->structure->id};
     * $lks->response->addMessage(lks_lang('"%entity_title" đã được kích hoạt thành công', ['%entity_title' => $entity->title]));
     * }
     */
}