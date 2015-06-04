<?php namespace Kalephan\User;

use Kalephan\LKS\EntityForm;

class UserForm {

	use EntityForm {
		formCreate as formCreateTrait;
	}

	public function __construct() {
		$this->entity = new UserEntity();
		$this->structure = $this->entity->structure();
	}

	public function formCreate($form) {
		$this->formCreateTrait($form);

		$form->fields['email']['#validate'] .= '|unique:users';
	}

	public function formAdd($form) {
		$this->formCreate($form);

		unset($form->fields['password']);
		$form->fields['active']['#default'] = 1;
		array_unshift($form->submit, get_called_class() . '@formAddSubmit');
	}

	public function formAddSubmit($form, &$form_values) {
		$form_values['password'] = str_random(8);
	}
}