<div id="shop_topic_create" class="clearfix">
	@if(count($form->error))
	<div class="message_delete clearfix">
		<span> {{ lks_template_item_list($form->error) }} </span>
	</div>
	@endif

	<div class="tab_title_main">
		<i class="icon_arrow_grey" style="margin-top: 12px;"></i><a
			href="{{lks_url('{userpanel}/topic/create/start')}}">Chọn Danh Mục
			Khác</a><span class="txt_helpRight"><i class="icon_arrow_grey"></i><a
			href="{{lks_url('{frontend}/article/9')}}" target="_blank">Hướng dẫn
				đăng bán sản phẩm</a></span>
	</div>

	<div class="box_post mar_bottom7">
		{{ lks_form_open($form->form) }}

		<div class="sub_post">{{ lks_form_render('category_text', $form) }}</div>

		{{ lks_form_render('province_id', $form) }}

		<div class="title_post clearboth">
			<span>THÔNG TIN SẢN PHẨM</span>
		</div>
		<div class="box_input_post">
			{{ lks_form_render('title', $form) }} {{
			lks_form_render('short_desc', $form) }}

			<div class="box_input_post_left">
				{{ lks_form_render('price', $form) }} {{ lks_form_render('shipping',
				$form) }} {{ lks_form_render('is_promotion', $form) }}

				<div id="datepicker_group_0">
					<div class="form_item_coupononproduct_value">{{
						lks_form_render('coupon_value', $form) }}</div>
					<div class="form_item_coupononproduct_type">{{
						lks_form_render('coupon_type', $form) }}</div>
					<div class="form_item_coupononproduct_start">{{
						lks_form_render('coupon_start', $form) }}</div>
					<div class="form_item_coupononproduct_end">{{
						lks_form_render('coupon_end', $form) }}</div>
				</div>
			</div>

			<div class="box_input_post_right">{{ lks_form_render('image', $form)
				}}</div>
		</div>

		<div class="title_post">
			<span>MÔ TẢ CHI TIẾT</span>
		</div>
		<div class="box_editor">{{ lks_form_render('content', $form) }}</div>

		<div class="title_post">
			<span>THÊM SẢN PHẨM</span>
		</div>
		<table>
			<tbody>
				@for($i = 1; $i <= $form['#settings']['product_items']; $i++)
				<tr id="products_row_{{$i}}"
					class="products_row products_row_group_{{ceil($i/5)}} @if (ceil($i/5) != 1) hideMe @endif">
					<td align="center">
						<div class="label_number">{{$i}}</div> <img
						@if($form[$form['#settings']['product_group'] . $i][$form['#settings']['product_prefix'] . "image_$i"]['#value']) data-original="{{$form[$form['#settings']['product_group'] . $i][$form['#settings']['product_prefix'] . "
						image_$i"]['#value']}}" @endif width="65" height="65"
						class="rfm_image_upload_img lazy" />
					</td>
					<td align="center">{{
						lks_form_render($form['#settings']['product_prefix'] . "title_$i",
						$form[$form['#settings']['product_group'] . $i]) }} {{
						lks_form_render($form['#settings']['product_prefix'] .
						"shor_desc_$i", $form[$form['#settings']['product_group'] . $i])
						}}
						{{lks_instance_get()->load('\Kalephan\RFM\RFM')->renderUploadButton('fii_'
						. $form['#settings']['product_prefix'] . "image_$i" . '_field',
						'Up Ảnh')}} <a href="#" class="product_include"
						data-label="{{$i}}">Nhúng</a>
					</td>
					<td align="center">{{
						lks_form_render($form['#settings']['product_prefix'] . "price_$i",
						$form[$form['#settings']['product_group'] . $i]) }}</td>
					<td align="center" id="datepicker_group_{{$i}}">{{
						lks_form_render($form['#settings']['product_prefix'] .
						"coupon_value_$i", $form[$form['#settings']['product_group'] .
						$i]) }} {{ lks_form_render($form['#settings']['product_prefix'] .
						"coupon_type_$i", $form[$form['#settings']['product_group'] . $i])
						}} {{ lks_form_render($form['#settings']['product_prefix'] .
						"coupon_start_$i", $form[$form['#settings']['product_group'] .
						$i]) }} {{ lks_form_render($form['#settings']['product_prefix'] .
						"coupon_end_$i", $form[$form['#settings']['product_group'] . $i])
						}}</td>
					<td align="center">{{
						lks_form_render($form['#settings']['product_prefix'] .
						"price_sell_$i", $form[$form['#settings']['product_group'] . $i])
						}}</td>
					<td align="center" class="bg_function">{{
						lks_form_render_all($form[$form['#settings']['product_group'] .
						$i]) }} <a href="#"><i class="iconDelete"></i></a>
					</td>
				</tr>
				@endfor
			</tbody>
		</table>

		<nav>
			<ul class="pagination">
				@for($i = 1; $i <= ceil($form['#settings']['product_items']/5);
				$i++)
				<li @if ($i== 1) class="active" @endif><a href="#"
					data-group="{{$i}}">{{$i}}</a></li> @endfor
			</ul>
		</nav>

		{{ lks_form_render_all($form->actions) }} {{
		lks_form_render_all($form) }} {{ lks_form_close() }}
	</div>
</div>
</div>