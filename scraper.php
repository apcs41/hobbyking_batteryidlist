<?PHP
require 'scraperwiki.php';
require 'scraperwiki/simple_html_dom.php';

function _log($str) { echo $str."\n"; }

/* Sanity - Max number of pages to scrape. As of 09/27/2015 there is only 49 pages. */
$max_pages = 100;

for($page = 1; $page <= $max_pages; $page++) {
    _log('Retrieving Lipo Page: ' . $page);
    
    $url = 'http://www.hobbyking.com/hobbyking/store/lipofinderajax.asp?warehouseid=HK&column1=row2&column2=row4' . 
		   '&column3=row3&pCapacityMin=0&pCapacityMax=6000&pCapoverMax=0&pConfig=1&pDischargeMin=0&pDischargeMax=110'. 
		   '&pWeightMin=0&pWeightMax=2000&pAmin=0&pAmax=350&pBmin=0&pBmax=350&pCmin=0&pCmax=350&sqlcount=+top+(20)+&pageNumber=' . $page;
    
    $html = scraperWiki::scrape($url);
    
    $dom = new simple_html_dom();
    $dom->load($html);
	
    $DOM_batteries = $dom->find('table.result td table tbody tr');
    
	/* Remove first element (The sort row) */
	array_shift($DOM_batteries);
	
    foreach($DOM_batteries as $data) {
        $id    = intval(str_replace('uh_viewItem.asp?idProduct=', '', $data->children(1)->childNodes(0)->getAttribute('href')));
		$cells = trim($data->children(2)->plaintext);
		
        scraperwiki::save(['id'], [
			'id'    => $id, 
			'cells' => $cells
		]);
    }
	
	/* Check to see if we're on the last page */
	$DOM_pages = $dom->find('.resultPager');
	if($page >= intval($DOM_pages[4]->plaintext)) {
		_log('Completed');
		die();
	}
}

_log('Error: Max pages reached!');
die();