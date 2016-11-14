<?php

add_filter('the_title', 'mt_the_title', 10, 2);
function mt_the_title( $title, $id ) {
	global $view;

	// TODO: Remove this hack
	$viewType = get_query_var('mtView');
	if (!empty($viewType) && !isset($view)) {
		set_view($viewType);
	}

	if ($id == get_the_ID() && method_exists($view, 'getTitle')) {
		return $view->getTitle();
	}
	return $title;
}

add_filter('body_class', 'mt_body_class');
function mt_body_class( $classes ) {
	global $view;
	if (method_exists($view, 'checkWidescreen') && $view->checkWidescreen()) {
		return array_merge($classes, array('widescreen'));
	}
	return $classes;
}

add_filter('the_content', 'mt_the_content');
function mt_the_content( $content ) {
	global $view;
	if (method_exists($view, 'outputContent')) {
		return $view->outputContent();
	}
	return $content;
}

add_filter('mtTheme_breadcrumb_items', 'mt_mtTheme_breadcrumb_items');
function mt_mtTheme_breadcrumb_items( $items ) {
	global $view;
	if (method_exists($view, 'getBreadcrumb')) {
		return array_merge($items, $view->getBreadcrumb());
	}
	return $items;
}

function set_view($viewType) {
	require_once(MT_DIR . '/src/front-end/view/Common.php');
	global $view;

	$id = intval(get_query_var('mtId'));

	switch ($viewType) {
		case 'bilder/galerie':
			require_once(MT_DIR.'/src/front-end/view/gallery/Gallery.php');
			require_once(MT_DIR.'/src/front-end/view/gallery/StaticGallery.php');
			$view = new MT_View_StaticGallery($id, get_query_var('mtPage', 1), get_query_var('mtNum', 10), get_query_var('mtSort', 'date'));
			break;
		case 'bilder/tag':
			require_once(MT_DIR.'/src/front-end/view/gallery/Gallery.php');
			require_once(MT_DIR.'/src/front-end/view/gallery/TagGallery.php');
			$view = new MT_View_TagGallery(get_query_var('mtTag'));
			break;
		case 'bilder/kategorie':
			require_once(MT_DIR.'/src/front-end/view/Category.php');
			$view = new MT_View_Category($id);
			break;
		case 'fotograf':
			require_once(MT_DIR.'/src/front-end/view/Photographer.php');
			$view = new MT_View_Photographer($id);
			break;
//		default:
//			header('HTTP/1.0 404 Not Found');
	}
}