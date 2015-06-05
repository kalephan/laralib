<?php
namespace Chovip\Ec;

use Kalephan\Category\CategoryEntity as LKSCategoryEntity;
use Kalephan\Ec\Category\CategoryEntity;
use Kalephan\Ec\Topic\TopicEntity as LKSEcTopic;
use Kalephan\Core\Form;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class TopicEvent
{

    public function alterEntityStructureEcTopic(&$structure)
    {
        $structure->fields['category_id'] = array(
            '#name' => 'category_id',
            '#title' => lks_lang('Danh mục cấp 3'),
            '#type' => 'select',
            '#reference' => array(
                'name' => 'category',
                'class' => '\Kalephan\Ec\Category\CategoryEntity'
            ),
            '#options' => [],
            '#list_hidden' => true,
            '#validate' => 'required|numeric',
            '#attributes' => array(
                'size' => 20
            )
        );
        
        $structure->fields['shipping'] = array(
            '#name' => 'shipping',
            '#title' => lks_lang('Phí vận chuyển'),
            '#type' => 'select',
            '#options' => array(
                0 => lks_lang('Phí vận chuyển'),
                1 => lks_lang('Mễn phí'),
                2 => lks_lang('Liên hệ')
            )
        );
        
        $structure->fields['is_promotion'] = array(
            '#name' => 'is_promotion',
            '#title' => lks_lang('Đăng ký sản phẩm này hiển thị trên trang khuyến mãi'),
            '#type' => 'checkbox'
        );
        
        $structure->fields['province_id'] = array(
            '#name' => 'province_id',
            '#title' => lks_lang('Khu vực'),
            '#type' => 'select',
            '#reference' => array(
                'name' => 'category',
                'class' => '\Kalephan\Category\CategoryEntity'
            ),
            '#options_callback' => array(
                'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
                'arguments' => array(
                    'id' => 'location_province',
                    'parent' => '',
                    'select_text' => lks_lang('--- Toàn quốc ---')
                )
            ),
            '#list_hidden' => true,
            '#validate' => 'numeric'
        );
    }

    public function showStart($lks)
    {
        $lks->response->addContent(Form::build('\Chovip\Ec\\TopicEvent@showStartForm'));
    }

    public function showStartForm()
    {
        $form = [];
        $form['#theme'] = 'form-ec-topic-start';
        $form->submit[] = '\Chovip\Ec\\TopicEvent@showStartFormSubmit';
        $form['#redirect'] = lks_url('{userpanel}/topic/create');
        
        $form['category_level_1'] = array(
            '#name' => 'category_level_1',
            '#title' => lks_lang('Danh mục cấp 1'),
            '#type' => 'select',
            '#options_callback' => array(
                'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
                'arguments' => array(
                    'id' => 'product_level_1',
                    'parent' => ''
                )
            ),
            '#ajax' => array(
                'path' => 'product-category/children?group_name=product_level_2&parent_name=category_level_1&child_value=category_level_2_value&child_name=category_level_2',
                'wrapper' => 'fii_category_level_2',
                'autoload' => 1
            ),
            '#attributes' => array(
                'size' => 20
            )
        );
        
        $form['category_level_2'] = array(
            '#name' => 'category_level_2',
            '#type' => 'select',
            '#options' => [],
            '#ajax' => array(
                'path' => 'product-category/children?group_name=product_level_3&parent_name=category_level_2&child_value=category_level_3_value&child_name=category_level_3',
                'wrapper' => 'fii_category_level_3',
                'autoload' => 0
            ),
            '#attributes' => array(
                'size' => 20
            )
        );
        
        $form['category_level_3'] = array(
            '#name' => 'category_level_3',
            '#type' => 'select',
            '#options' => [],
            '#validate' => 'required|numeric',
            '#attributes' => array(
                'size' => 20
            )
        );
        
        $form['submit'] = array(
            '#name' => 'submit',
            '#type' => 'submit',
            '#value' => lks_lang('Bắt đầu đăng tin &gt;'),
            '#attributes' => [
                'class' => 'hideMe'
            ]
        );
        
        return $form;
    }

    public function showStartFormSubmit($form_id, &$form, &$form_values)
    {
        Session::set('shop-topic-create-start', $form_values['category_level_3']);
    }

    public function alterCreateForm($form_id, &$form)
    {
        $form['#theme'] = 'form-ec-topic-create';
        array_push($form->validate, '\Chovip\Ec\\TopicEvent@alterCreateFormValidate');
        array_push($form->submit, '\Chovip\Ec\\TopicEvent@alterCreateFormSubmit');
        
        $form['category_text'] = array(
            '#name' => 'category_text',
            '#type' => 'markup'
        );
        
        $form['category_id'] = array(
            '#name' => 'category_id',
            '#type' => 'hidden',
            '#disabled' => true
        );
        
        unset($form['province_id']['#title'], $form['shipping']['#title'], $form['coupon_type']['#title'], $form['content']['#title']);
        
        $form['coupon_start']['#title'] = lks_lang('Thời gian khuyến mãi');
        $form['coupon_end']['#title'] = lks_lang('đến');
        
        $form['coupon_start']['#attributes']['class'] .= 'datepicker datepicker_start';
        $form['coupon_end']['#attributes']['class'] .= 'datepicker datepicker_end';
        
        // ----- BEGIN Product items -----
        $form['#settings'] = array(
            'product_items' => 25,
            'product_prefix' => 'products_',
            'product_group' => '#products_'
        );
        
        $product_form = lks_instance_get()->load('Kalephan\Ec\Product\ProductEntity')->getStructure();
        $product_form = $product_form['#fields'];
        
        unset($product_form['title']['#title']);
        $product_form['title']['#attributes'] = array(
            'placeholder' => lks_lang('Tên sản phẩm'),
            'class' => 'width335'
        );
        unset($product_form['shor_desc']['#title']);
        $product_form['shor_desc']['#attributes'] = array(
            'placeholder' => lks_lang('Mô tả ngắn'),
            'class' => 'width335'
        );
        unset($product_form['price']['#title']);
        $product_form['price']['#attributes'] = array(
            'placeholder' => lks_lang('Nhập giá gốc sản phẩm'),
            'class' => 'width160'
        );
        $product_form['image'] = array(
            '#name' => 'image',
            '#type' => 'hidden',
            '#attributes' => [
                'class' => 'img_upload'
            ]
        );
        unset($product_form['active']);
        unset($product_form['coupon_value']['#title']);
        $product_form['coupon_value']['#attributes'] = [
            'placeholder' => lks_lang('Khuyến mãi')
        ];
        unset($product_form['coupon_type']['#title']);
        unset($product_form['coupon_start']['#title']);
        $product_form['coupon_start']['#attributes'] = [
            'placeholder' => lks_lang('Thời điểm bắt đầu')
        ];
        $product_form['coupon_start']['#attributes']['class'] .= 'datepicker datepicker_start';
        unset($product_form['coupon_end']['#title']);
        $product_form['coupon_end']['#attributes'] = [
            'placeholder' => lks_lang('Thời điểm kết thúc')
        ];
        $product_form['coupon_end']['#attributes']['class'] .= 'datepicker datepicker_end';
        $product_form['price_sell'] = array(
            '#name' => 'price_sell',
            '#type' => 'text',
            '#attributes' => array(
                'placeholder' => lks_lang('Giá bán sản phẩm'),
                'class' => 'width160'
            )
        );
        
        for ($i = 1; $i <= $form['#settings']['product_items']; $i ++) {
            $product_group = "#products_$i";
            $form['#group'][] = $product_group;
            $form[$product_group] = [];
            foreach ($product_form as $key => $value) {
                if (isset($value['#type'])) {
                    if ($value['#name'] == 'image') {
                        $value['#attributes']['data-label'] = $i;
                    }
                    
                    $product_name = $form['#settings']['product_prefix'] . $value['#name'] . '_' . $i;
                    $value['#name'] = $product_name;
                    // $value['#theme'] = 'form_item-topic-create-product';
                    
                    $form[$product_group][$product_name] = $value;
                }
            }
        }
        // ----- END Product items -----
        
        // k($form);
    }

    public function alterCreateFormValue($form_id, &$form, &$form_values)
    {
        $lks = lks_instance_get();
        
        $form_values['category_id'] = Session::get('shop-topic-create-start', 0);
        
        if (! $form_values['category_id']) {
            App::abort(403);
        }
        
        // category_text value
        $category = $lks->load('\Kalephan\Ec\Category\CategoryEntity');
        $parents = $category->getParents($form_values['category_id']);
        $form_values['category_text'] = '';
        if ($parents) {
            foreach ($parents as $cat) {
                $form_values['category_text'] .= lks_anchor("s?c3=" . $cat->id, $cat->title, [
                    'target' => '_blank'
                ]);
            }
        }
        
        $lks->asset->jsAdd([
            'topic_create_datepicker_group' => $form['#settings']['product_items']
        ], 'settings');
    }

    public function alterCreateFormValidate($form_id, &$form, &$form_values)
    {
        return true;
    }

    public function alterCreateFormSubmit($form_id, &$form, &$form_values)
    {}
}
