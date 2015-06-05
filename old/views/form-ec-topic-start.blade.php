<div id="shop_topic_create" class="clearfix">
	@if(count($form->error))
	<div class="message_delete clearfix">
		<span> {{ lks_template_item_list($form->error) }} </span>
	</div>
	@endif {{ lks_form_open($form->form) }}

	<div class="tab_list_step">
		<ul>
			<li><a href="#" class="active_list_step">Lựa Chọn Danh Mục Bạn Muốn
					Đăng Tin</a></li>
			<!-- <li><a href="#">Các Danh Mục Đã Đăng Tin</a></li> -->
		</ul>
	</div>

	<div class="wrapper_box_list_step box_content mar_bottom7">
		<div class="categories_select_boxes">
			<div id="category_level_1_group">{{
				lks_form_render('category_level_1', $form) }}</div>
			<div id="category_level_2_group">
				<span class="glyphicon glyphicon-chevron-right iconArrowRight"></span>
				{{ lks_form_render('category_level_2', $form) }}
			</div>
			<div id="category_id_group">
				<span class="glyphicon glyphicon-chevron-right iconArrowRight"></span>
				{{ lks_form_render('category_level_3', $form) }}
			</div>
		</div>
		<div class="categories_sub">{{ lks_form_render_all($form->actions) }}
		</div>
	</div>

	{{ lks_form_render_all($form) }} {{ lks_form_close() }}
</div>