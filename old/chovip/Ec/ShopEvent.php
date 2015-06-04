<?php
namespace Chovip\Ec;

use Kalephan\LKS\Approve\ApproveEntity;
use Kalephan\Profile\ProfileEntity;
use Kalephan\Ec\Shop\ShopEntity;
use Kalephan\User\UserEntity;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class ShopEvent {

	public function alterEntityStructureEcShop(&$structure) {
		$structure['#approve'] = true;

		$structure['#action_links'] = array(
			'list' => '{backend}/shop/list',
            'read' => '{frontend}/shop/%',
            'update' => '{userpanel}/shop/%/update',
            'delete' => '{backend}/shop/%/delete',
            //'preview' => '{userpanel}/shop/%/preview',
            'approve' => '{backend}/shop/%/approve',
            'active' => '{backend}/shop/%/active',
		);

		$structure->fields['path']['#attributes']['data-prefix'] = 'http://chovip.vn/';
		$structure->fields['path']['#attributes']['class'] .= ' form-prefix';

		$structure->fields['shop_address'] = array(
			'#name' => 'shop_address',
			'#title' => lks_lang('Địa chỉ'),
			'#type' => 'text',
			'#required' => true,
			'#validate' => 'required|max:100',
			'#attributes' => array(
				'placeholder' => lks_lang('123 Đại lộ Bình Dương, P.Chánh Nghĩa'),
				'data-required' => '',
				'size' => 100,
			),
			'#list_hidden' => 1,
			'#description' => lks_lang('Không bao gồm tên tỉnh thành và quận huyện trong địa chỉ.'),
			'#error_message' => lks_lang('Trường này yêu cầu phải nhập.'),
		);
		$structure->fields['shop_province_id'] = array(
			'#name' => 'shop_province_id',
			'#title' => lks_lang('Khu vực'),
			'#type' => 'select',
			'#reference' => array(
				'name' => 'category',
				'class' => '\Kalephan\Category\CategoryEntity',
			),
			'#options_callback' => array(
				'class' => '\Kalephan\Category\CategoryEntity@loadOptionsAll',
				'arguments' => array(
					'id' => 'location_province',
					'parent' => '',
					'select_text' => lks_lang('--- Tỉnh/Thành ---'),
				),
			),
			'#ajax' => array(
				'path' => 'location/district?district_name=shop_district_id&province_name=shop_province_id&district_value=shop_district_id_value',
				'wrapper' => 'fii_shop_district_id',
				'autoload' => 1,
			),
			'#list_hidden' => true,
			'#required' => true,
			'#validate' => 'required|numeric',
		);
		$structure->fields['shop_district_id'] = array(
			'#name' => 'shop_district_id',
			'#type' => 'select',
			'#reference' => array(
				'name' => 'category',
				'class' => '\Kalephan\Category\CategoryEntity',
			),
			'#options' => array('' => lks_lang('--- Quận/Huyện ---')),
			'#list_hidden' => true,
			'#required' => true,
			'#validate' => 'required|numeric',
		);
		$structure->fields['shop_homephone'] = array(
			'#name' => 'shop_homephone',
			'#title' => lks_lang('Điện thoại bàn'),
			'#type' => 'text',
			'#list_hidden' => 1,
		);
		$structure->fields['shop_mobile'] = array(
			'#name' => 'shop_mobile',
			'#title' => lks_lang('Điện thoại di động'),
			'#type' => 'text',
			'#required' => true,
			'#validate' => 'required|regex:/^[0-9+]{10,15}$/',
			'#list_hidden' => 1,
			'#attributes' => array(
				'placeholder' => lks_lang('0912345678'),
				'data-required' => '',
				'data-pattern' => '^[0-9+]{10,13}$',
			),
			'#description' => lks_lang('Hệ thống chỉ hỗ trợ các mạng sau: Mobifone, Vinaphone, Viettel. <br />Số điện thoại này được sử dụng để gửi tin nhắn kích hoạt shop và sẽ được ChoVip.vn dùng làm số điện thoại liên lạc đến shop. Vì vậy, vui lòng sử dụng số điện thoại mà bạn dùng thường xuyên để gửi tin nhắn.'),
			'#error_message' => lks_lang('Số di động từ 10 - 15 ký tự và chỉ có ký tự số, dấu +'),
		);
		$structure->fields['shop_website'] = array(
			'#name' => 'shop_website',
			'#title' => lks_lang('Website'),
			'#type' => 'text',
			'#list_hidden' => 1,
			'#validate' => 'url',
			'#attributes' => array(
				'placeholder' => lks_lang('http://google.com'),
				'data-pattern' => '^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$',
			),
			'#error_message' => lks_lang('Đây không phải là URL hợp lệ của trang web.'),
		);
		$structure->fields['shop_chat_nick'] = array(
			'#name' => 'shop_chat_nick',
			'#title' => lks_lang('Nick chat'),
			'#type' => 'hidden',
			'#list_hidden' => 1,
		);
		$structure->fields['shop_image'] = array(
			'#name' => 'shop_image',
			'#title' => lks_lang('Biểu tượng shop'),
			'#type' => 'file',
			'#widget' => 'image',
			'#style' => 'shop_avatar',
			'#empty_field_ajax_url' => 'shop/%id/empty-field/shop_image',
			'#list_hidden' => true,
			'#validate' => 'image|mimes:jpeg,png,gif',
			'#description' => lks_lang('Kích thước (90x90 pixels).'),
			//'#attributes' => ['disabled' => 'disabled'],
		);
		$structure->fields['approve'] = array(
			'#name' => 'approve',
			'#title' => lks_lang('Mã phê duyệt'),
			//'#type' => 'hidden',
			'#list_hidden' => 1,
			//'#form_hidden' => 1,
			'#display_hidden' => 1,
		);

		$structure->fields['active']['#options'][1] = lks_lang('Đã được phê duyệt');
		$structure->fields['active']['#options'][2] = lks_lang('Đã kích hoạt Điện thoại di động');
	}

	public function allowConfirmation() {
		return $this->_alowConfirmationFinalize();
	}

	public function allowFinalize() {
		if ($this->_alowConfirmationFinalize()) {
			$lks =& lks_instance_get();
			$id = intval($lks->request->segment(1));
			$shop = $lks->load('\Kalephan\Ec\Shop\ShopEntity')->loadEntity($id);

			// After mobile validate
			if ($shop->active == 2) {
				return true;
			}
		}

		return false;
	}

	private function _alowConfirmationFinalize() {
		$lks =& lks_instance_get();

		$id = intval($lks->request->segment(1));
		if ($lks->load('\Kalephan\Ec\Shop\ShopEntity')->loadEntity($id)) {
			return true;
		}

		return false;
	}

	public function alterCreateForm($form_id, &$form) {
		$lks =& lks_instance_get();

		array_unshift($form->validate, '\Chovip\Ec\ShopEvent@alterCreateFormValidate');
		$form->submit[] = '\Chovip\Ec\ShopEvent@alterCreateFormSubmit';
		$form['#theme'] = 'form-ec-shop-create';

		$form['#group'][] = '#group_owner';
		$form['#group_owner'] = [];

		$form['#group'][] = '#group_shop';
		$form['#group_shop'] = [];

		$form['#group_shop']['title'] = $form['title'];
		$form['#group_shop']['path'] = $form['path'];
		unset($form['title'], $form['path']);

		foreach ($form as $key => $value) {
			if (strpos($key, 'shop_') !== false) {
				$form['#group_shop'][$key] = $value;
				unset($form[$key]);
			}
		}

		$form['#group_shop']['the_same_owner_and_shop'] = array(
			'#name' => 'the_same_owner_and_shop',
			'#type' => 'checkbox',
			'#value' => 1,
			'#title' => lks_lang('Thông tin chủ shop trùng với thông tin shop'),
			'#validate' => 'accepted',
			'#attributes' => array(
				'checked' => 'checked',
			),
			'#weight' => -99,
		);

		$user_obj = $lks->load('\Kalephan\User\UserEntity');
		$user_structure = $user_obj->getStructure();

		$profile_obj = $lks->load('\Kalephan\Profile\ProfileEntity');
		$profile_structure = $profile_obj->getStructure();

		$form['#group_owner']['fullname'] = $user_structure->fields['fullname'];
		$form['#group_owner']['fullname']['#weight'] = -90;
		$form['#group_owner']['gender'] = $profile_structure->fields['gender'];
		$form['#group_owner']['gender']['#weight'] = -80;
		$form['#group_owner']['birthday'] = $profile_structure->fields['birthday'];
		$form['#group_owner']['birthday']['#weight'] = -70;
		$form['#group_owner']['cmnd'] = $profile_structure->fields['cmnd'];
		$form['#group_owner']['cmnd']['#weight'] = -60;
		$form['#group_owner']['address'] = $profile_structure->fields['address'];
		$form['#group_owner']['address']['#weight'] = -50;
		$form['#group_owner']['province_id'] = $profile_structure->fields['province_id'];
		$form['#group_owner']['province_id']['#weight'] = -40;
		$form['#group_owner']['province_id']['#ajax'] = array(
			'path' => 'location/district?district_name=district_id&province_name=province_id&district_value=district_id_value',
			'wrapper' => 'fii_district_id',
			'autoload' => 1,
		);
		$form['#group_owner']['district_id'] = $profile_structure->fields['district_id'];
		$form['#group_owner']['district_id']['#weight'] = -30;
		$form['#group_owner']['district_id_value'] = array(
			'#name' => 'district_id_value',
			'#type' => 'hidden',
		);
		$form['#group_owner']['homephone'] = $profile_structure->fields['homephone'];
		$form['#group_owner']['homephone']['#weight'] = -20;
		$form['#group_owner']['mobile'] = $profile_structure->fields['mobile'];
		$form['#group_owner']['mobile']['#weight'] = -10;
		$form['#group_owner']['email'] = $user_structure->fields['email'];
		$form['#group_owner']['email']['#weight'] = 1;
		$form['#group_owner']['email']['#disabled'] = true;
		$form['#group_owner']['email']['#attributes']['disabled'] = 'disabled';
		unset($form['#group_owner']['email']['#description']);

		$form['#group_shop']['shop_district_id_value'] = array(
			'#name' => 'shop_district_id_value',
			'#type' => 'hidden',
		);

		$this->_chatNickForm($form['#group_shop']);

		$form['#group_shop']['shop_image']['#weight'] = 97;

		$form['#group_shop']['accepted'] = array(
			'#name' => 'accepted',
			'#type' => 'checkbox',
			'#value' => 1,
			'#title' => lks_lang('Tôi đã đọc và đồng ý với những :term', array(
				':term' => lks_anchor('article/8', lks_lang('quy định và điều khoản sử dụng shop'), array('target' => '_blank')),
			)),
			'#validate' => 'accepted',
			'#attributes' => array(
				'data-required' => '',
			),
			'#weight' => 98,
			'#error_message' => lks_lang('Bạn phải đọc và đồng ý với các quy định của chúng tôi mới có thể mở shop.'),
		);

		$form['#group_shop']['submit'] = $form->actions['submit'];
		$form['#group_shop']['submit']['#attributes']['class'] = 'bg_button_or button_or';
		$form['#group_shop']['submit']['#value'] = lks_lang('Xác nhận thông tin');
		$form['#group_shop']['submit']['#weight'] = 99;
		unset($form->actions['submit']);

		unset($form['#message']);
	}

	private function _chatNickForm(&$form) {
		$form['shop_chat_nick_value'] = array(
			'#name' => 'shop_chat_nick_value[]',
			'#title' => lks_lang('Nick chat'),
			'#type' => 'text',
			'#attributes' => ['maxlength' => 50],
		);

		$form['shop_chat_nick_type'] = array(
			'#name' => 'shop_chat_nick_type[]',
			'#type' => 'select',
			'#options' => [
				'yahoo' => lks_lang('Yahoo'),
				'skype' => lks_lang('Skype'),
			],
		);

		$form['shop_chat_nick_add'] = array(
			'#name' => 'shop_chat_nick_add',
			'#type' => 'markup',
			'#title' => '&nbsp;',
			'#value' => '<a id="nickchat_add" href="#" class="icon_add padding_left_15">' . lks_lang('Thêm Nick Chat') . '</a>',
		);
	}

	public function alterCreateFormValue($form_id, &$form, &$form_values) {
		$lks =& lks_instance_get();

		$userid = Auth::id();

		$user = $lks->load('\Kalephan\User\UserEntity');
		$user = $user->loadEntity($userid);
		$form_values['fullname'] = !empty($user->fullname) ? $user->fullname : '';
		$form_values['email'] = !empty($user->email) ? $user->email : '';

		$profile = $lks->load('\Kalephan\Profile\ProfileEntity');
		$profile = $profile->loadEntity($userid);
		$form_values['gender'] = !empty($profile->gender) ? $profile->gender : '';
		$form_values['birthday'] = !empty($profile->birthday) ? $profile->birthday : '';
		$form_values['address'] = !empty($profile->address) ? $profile->address : '';
		$form_values['province_id'] = !empty($profile->province_id) ? $profile->province_id : '';
		$form_values['district_id'] = !empty($profile->district_id) ? $profile->district_id : '';
		$form_values['mobile'] = !empty($profile->mobile) ? $profile->mobile : '';
		$form_values['district_id_value'] = !empty($profile->district_id) ? $profile->district_id : '';
		if (!empty($profile->district_id) && empty($form_values['shop_district_id_value'])) {
			$form_values['shop_district_id_value'] = $profile->district_id;
		}
	}

	public function alterCreateFormValidate($form_id, &$form, &$form_values) {
		if (substr($form_values['shop_mobile'], 0, 1) != '+') {
			if (substr($form_values['shop_mobile'], 0, 1) == '0') {
				$form_values['shop_mobile'] = substr($form_values['shop_mobile'], 1);
			}

			$form_values['shop_mobile'] = "+84" . $form_values['shop_mobile'];
		}

		$form_values['path'] = lks_str_slug($form_values['path']);

		return $this->alterUpdateFormValidate($form_id, $form, $form_values);
	}

	public function alterCreateFormSubmit($form_id, &$form, &$form_values) {
		$lks =& lks_instance_get();
		$userid = Auth::id();

		// Save User Entity (role & fullname)
		$role_id = config('lks.ec shop role salesman', []);
		$role_id = reset($role_id);
		if ($role_id) {
			$user_obj = $lks->load('\Kalephan\User\UserEntity');
			$user = $user_obj->loadEntity($userid);
			$user->role[] = $role_id;
			$user->role = array_unique($user->role);
			$user->title = $form_values["fullname"];
			$user_obj->saveEntity($user);
		}

		// Save User Profile
		$profile_obj = $lks->load('\Kalephan\Profile\ProfileEntity');
		if (!$profile = $profile_obj->loadEntity($userid)) {
			$profile = new \stdClass;
		}

		$profile_structure = $profile_obj->getStructure();
		foreach ($profile_structure->fields as $key => $value) {
			if (isset($form_values[$key])) {
				$profile->{$key} = $form_values[$key];
			}
		}
		$profile->id = $userid;
		$profile_obj->saveEntity($profile);

		$form['#redirect'] = lks_url('{userpanel}/shop/' . $form_values['id'] . '/confirmation');
	}

	public function alterUpdateForm($form_id, &$form) {
		array_unshift($form->validate, '\Chovip\Ec\ShopEvent@alterUpdateFormValidate');
		$form->submit[] = '\Chovip\Ec\ShopEvent@alterUpdateFormSubmit';

		$form['#theme'] = 'form-ec-shop-update';

		$form['#group'] = array(
			'#group_shop',
			'#group_paymenth',
			'#group_shipmenth',
			'#group_aboutus',
			'#group_contact',
			'#actions',
		);

		$form['#group_shop'] = [];
		$form['#group_paymenth'] = [];
		$form['#group_shipmenth'] = [];
		$form['#group_aboutus'] = [];
		$form['#group_contact'] = [];

		$form['#group_shop']['title'] = $form['title'];
		$form['#group_shop']['path'] = $form['path'];
		unset($form['title'], $form['path']);

		$form['#group_shop']['path']['#disabled'] = true;
		$form['#group_shop']['path']['#attributes']['disabled'] = 'disabled';

		$form_extend = lks_instance_get()->load('\Chovip\Ec\ShopExtendEntity')->getStructure();
		$form_extend = $form_extend['#fields'];

		$form['#group_paymenth']['shop_paymenth'] = $form_extend['shop_paymenth'];
		$form['#group_shipmenth']['shop_shipmenth'] = $form_extend['shop_shipmenth'];
		$form['#group_aboutus']['shop_aboutus'] = $form_extend['shop_aboutus'];
		$form['#group_contact']['shop_contact'] = $form_extend['shop_contact'];

		foreach ($form as $key => $value) {
			if (strpos($key, 'shop_') !== false) {
				$form['#group_shop'][$key] = $value;
				unset($form[$key]);
			}
		}

		$form['#group_shop']['shop_mobile']['#disabled'] = true;
		$form['#group_shop']['shop_mobile']['#attributes']['disabled'] = 'disabled';

		$form['#group_shop']['shop_district_id_value'] = array(
			'#name' => 'shop_district_id_value',
			'#type' => 'hidden',
		);

		$form['#group_shop']['shop_image']['#weight'] = 99;

		$this->_chatNickForm($form['#group_shop']);

		$form->actions['submit']['#value'] = lks_lang('Cập nhật');
		$form->actions['reset'] = array(
			'#name' => 'reset',
			'#type' => 'reset',
			'#value' => lks_lang('Nhập lại'),
		);
	}

	public function alterUpdateFormValue($form_id, &$form, &$form_values) {
		$lks =& lks_instance_get();
		$form_values['shop_district_id_value'] = !empty($form_values['shop_district_id']) ? $form_values['shop_district_id'] : '';

		$shop_extend = $lks->load('\Chovip\Ec\ShopExtendEntity');
		$shop_extend = $shop_extend->loadEntity($form_values['id']);
		if ($shop_extend) {
			if ($shop_extend->approve) {
				$approve = $lks->load('\Kalephan\LKS\Approve\ApproveEntity');
				if ($approve = $approve->loadEntity($shop_extend->approve)) {
					$shop_extend = $approve;
				}
			}

			$form_values['shop_paymenth'] = $shop_extend->shop_paymenth;
			$form_values['shop_shipmenth'] = $shop_extend->shop_shipmenth;
			$form_values['shop_aboutus'] = $shop_extend->shop_aboutus;
			$form_values['shop_contact'] = $shop_extend->shop_contact;
		}
	}

	public function alterUpdateFormValidate($form_id, &$form, &$form_values) {
		$form_values['shop_chat_nick'] = [];
		if (count($form_values['shop_chat_nick_value']) && count($form_values['shop_chat_nick_type'])) {
			foreach ($form_values['shop_chat_nick_value'] as $key => $value) {
				if (!empty($form_values['shop_chat_nick_value'][$key]) && !empty($form_values['shop_chat_nick_type'][$key])) {
					$form_values['shop_chat_nick'][] = $form_values['shop_chat_nick_type'][$key] . ':' . $form_values['shop_chat_nick_value'][$key];
				}
			}
		}
		$form_values['shop_chat_nick'] = implode('|', $form_values['shop_chat_nick']);
		unset($form_values['shop_chat_nick_value'], $form_values['shop_chat_nick_value']);

		if (!empty($form_values['shop_website'])
			 && substr($form_values['shop_website'], 0, 7) != 'http://'
			 && substr($form_values['shop_website'], 0, 8) != 'https://') {
			$form_values['shop_website'] = 'http://' . $form_values['shop_website'];
		}

		return true;
	}

	public function alterUpdateFormSubmit($form_id, &$form, &$form_values) {
		$shop_extend_obj = lks_instance_get()->load('\Chovip\Ec\ShopExtendEntity');
		$shop_extend_structure = $shop_extend_obj->getStructure();

		$shop_extend = new \stdClass;
		foreach ($shop_extend_structure->fields as $key => $value) {
			if (isset($form_values[$key])) {
				$shop_extend->$key = $form_values[$key];
			}
		}
		$shop_extend_obj->saveEntity($shop_extend);

		$form['#redirect'] = lks_url('{userpanel}/shop/' . $form_values['id'] . '/update');
	}

	public function showConfirmation($lks, $id) {
		$shop = $lks->load('\Kalephan\Ec\Shop\ShopEntity')->loadEntity($id, false);

		$vars = array(
			'mobile' => isset($shop->shop_mobile) ? str_replace('+84', 0, $shop->shop_mobile) : '',
			'path' => isset($shop->path) ? $shop->path : '',
			'id' => $id,
		);

		$lks->response->addContent(lks_render('ec-shop-confirmation', $vars));
	}

	public function showFinalize($lks) {
		$lks->response->addContent(lks_render('ec-shop-finalize'));
	}

	public function showRead($lks, $entity_id) {
		return 'UnderConstruction';
		/*$shop = $lks->load('\Kalephan\Ec\Shop\ShopEntity');
		$entity = $shop->loadEntityByPath($entity_id);

		if (!$entity) {
			App::abort(404);
		}

		$shop->showRead($lks, $entity->id);*/
	}

	public function showApprove($lks, $entity_id) {
		$approve = $lks->load('\Kalephan\LKS\Approve\ApproveEntity');

		$approve->approve('\Kalephan\Ec\Shop\ShopEntity', $entity_id);
		$approve->approve('\Chovip\Ec\ShopExtendEntity', $entity_id);
	}
}