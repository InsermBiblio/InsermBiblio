<?php
$context = Timber::get_context();

$context['services'] = Timber::get_posts(['category_name' => 'services']);
$context['infoFooter'] = Timber::get_posts(['category_name' => 'footer-info']);
$parentLinkID = get_cat_ID('liens utiles');
$useLinks = get_categories( array('child_of' => $parentLinkID, 'orderby' => 'term_id', 'order' => 'ASC',) );
foreach ($useLinks as $link) {
	$links=(array)$link;
	$context['useLinks'][] = [
		'title' => $links['name'],
		'nbcol' => (int)($links['count']/7+1),
		'posts' => Timber::get_posts([ 'category_name' => $links['slug'], 'orderby' => 'name', 'order' => 'ASC' ])
	];
}
// traitement sites thematiques
$category = get_queried_object();
$nicename = $category->slug;
$name = $category->name;
$parent = $category->parent;
$parentName = get_category($parent)->category_nicename;
$parentThematiques = get_cat_ID('sites thematiques');
$parentOmics = get_cat_ID('omics');
$context['category'] = array('nicename'=> $nicename,'name' => $name);

// traitement sous-categories publications
$parentPublications = get_cat_ID('publications');

if ($parent == $parentThematiques or $parent == $parentOmics) {
	$sitesThematiques = get_categories( array('child_of' => $parentThematiques, 'orderby' => 'name', 'order' => 'ASC') );
	foreach ($sitesThematiques as $link) {
		$links=(array)$link;
		if ($links['parent'] == $parentThematiques) {
			$context['sitesThematiques'][] = [
				'title' => $links['name'],
				'slug' => $links['slug'],
			];
		}
		elseif ($links['parent'] == $parentOmics) {
			$context['omics'][] =[
				'title' => $links['name'],
				'slug' => $links['slug']
			];
		}
	}
	$context['themes']=Timber::get_posts(['category_name' => $nicename, 'numberposts' => -1, 'orderby' => 'title', 'order' => 'ASC']);

    Timber::render('sites-thematiques.twig', $context);
}
elseif ($parent == $parentPublications or $nicename == "publications" ) {
	$sousPublications = get_categories( array('child_of' => $parentPublications, 'orderby' => 'name', 'order' => 'ASC') );
	foreach ($sousPublications as $link) {
		$links=(array)$link;
		if ($links['parent'] == $parentPublications) {
			$context['sousPublications'][] = [
				'title' => $links['name'],
				'slug' => $links['slug'],
				'parent' => $links['parent'],
				'sousposts' => Timber::get_posts([ 'category_name' => $links['slug'] ])
			];
			$exclusion[] = $links['cat_ID'];
		}
	}
	$context['publications'] = Timber::get_posts([ 'category_name' => 'publications', 'category__not_in' => $exclusion, 'orderby' => 'title', 'order' => 'ASC']);
	Timber::render('publications.twig', $context);
}
elseif ($nicename == "outils-gestion" or $parentName == "outils-gestion") {
	$context['biblio'] = Timber::get_posts(['category_name' => 'logiciels-bibliographiques']);
	$context['autres'] = Timber::get_posts(['category_name' => 'autres-outils']);
	Timber::render('outils.twig', $context);
}
elseif ($nicename == "ressources") {
	$context['ressources'] = Timber::get_posts([ 'category_name' => $nicename,  'orderby' => 'title', 'order' => 'ASC']);
	Timber::render('ressources.twig', $context);
}
elseif ($nicename == "aide") {
	Timber::render('aide.twig', $context);
}
elseif ($nicename == "actus") {
	if ( isset($_GET["show"]) and ($_GET["show"] == "archives")) {
		Timber::render('actus-archives.twig', $context);
	}
	else {
		Timber::render('actus.twig', $context);
	}
}
else {
    Timber::render('category.twig', $context);
}
