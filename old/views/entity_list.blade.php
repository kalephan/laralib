@if($add_new) {{ lks_anchor($add_new, lks_lang('Thêm mới')) }} @endif

{{lks_template_table($data);}}

<div id="search_showing">{{ lks_lang('Đang hiển thị từ %pager_from đến
	%pager_to trong tổng số %pager_total kết quả', array('%pager_from' =>
	$pager_from, '%pager_to' => $pager_to, '%pager_total' =>
	$pager_total))}}</div>

{{ lks_paganization($pager_page, ceil($pager_total /
$pager_items_per_page)) }}
