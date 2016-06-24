<?php

//Wordnet API
include_once 'WordnetApi.class.php';

//Define constants

define("DATABASE", "commerce");

//English Stop Words list (source http://www.ranks.nl/stopwords)
$stop_words = "a,about,above,after,again,against,all,am,an,and,any,are,aren't,as,at,be,because,been,before,being,below,between,both,but,by,can't,cannot,could,couldn't,did,didn't,do,does,doesn't,doing,don't,down,during,each,few,for,from,further,had,hadn't,has,hasn't,have,haven't,having,he,he'd,he'll,he's,her,here,here's,hers,herself,him,himself,his,how,how's,i,i'd,i'll,i'm,i've,if,in,into,is,isn't,it,it's,its,itself,let's,me,more,most,mustn't,my,myself,no,nor,not,of,off,on,once,only,or,other,ought,our,ours,	ourselves,out,over,own,same,shan't,she,she'd,she'll,she's,should,shouldn't,so,some,such,than,that,that's,the,their,theirs,them,themselves,then,there,there's,these,they,they'd,they'll,they're,they've,this,those,through,to,too,under,until,up,very,was,wasn't,we,we'd,we'll,we're,we've,were,weren't,what,what's,when,when's,where,where's,which,while,who,who's,whom,why,why's,with,won't,would,wouldn't,you,you'd,you'll,you're,you've,your,yours,yourself,yourselves";
$stop_words_array = explode(",", $stop_words);


//Generate queries
/*
 * synonym_queries function returns an array of queries like the following example
 * 
 * synonym_queries ("buy a phone")
 * 
 * it returns : an array where elements are the following : [0]=> purchase phone, [1]=>order phone, [2]=>buy cellular ...etc
 * 
 * ! The stop words as you see are skipped
 * 
 * */


function synonym_queries($search_query, $stop_words_array) {
$query = array();
$queries = array () ;
	foreach (explode(" ", $search_query) as $single_word) {
		if (!in_array($single_word, $stop_words_array)) {
			$query[] = $single_word;

		}
	}
	$q = implode(" ", $query);
	foreach ($query as $key => $word) {

// Get synonyms 
		$synonyms = WordnetApi::_new($word) -> getSynonyms();

		foreach ($synonyms as $synonym) {

			if (true == word_exists($synonym)) {

				$nq = str_replace($word, $synonym, $q);
				if (!in_array($nq, $queries)) {
					$queries[] = $nq;
				}
			} // end if(word_exists($synonym))
		} // end foreach ($synonyms as $synonym){
	}// end foreach ($query as $key=>$word)

	return $queries;

}// end function synonym_queries


// when you search for a keyword or keyphrase :
/*
 * 
 * Assuming now that you have a table named 'products' of products with three columns : ID, product_title, product_description
 *
 *  You can modify the sql query parameters depending on the structure of your products table
 * 
 * 
 * */

if (isset($_POST['search'])) {
	
	$my_search_queries = synonym_queries($_POST['search'], $stop_words_array);
	// the search results array

		$results_array  = array();
	
	foreach ($my_search_queries as $my_search_query){
		
		//$my_search_query = "order phone"
		
// THE RELEVANCE ALGORITHM
/*
 * THE RELEVANCE ALGORITHM : this algorithm will search for each word of $my_search_query and retruns a result
 * the result returned will be stored into an associatif array like the following : 	$results_array[article_id] = freq (freq = how many times this result has been found)
 *  * */
 		foreach (explode(" ", $my_search_query) as $sword){
			
			$mysqli = connect ();
			//you are free to put what you want as parameter in this query
			$q = 'SELECT ID FROM products WHERE product_title LIKE \'%'.$sword.'%\' AND product_description LIKE \'%'.$sword.'%\'';
			$result = $mysqli->query ($q);
		
			$table  = mysqli_fetch_array($result,MYSQL_ASSOC);
			if(!isset($table['ID'])){continue;}
			
			if(!isset($results_array[$table['ID']])){$results_array[$table['ID']] = 0; }


			$results_array [$table['ID']] = intval($results_array[$table['ID']]) + 1;
			//
 			
 		} // end foreach (explode(" ", $my_search_query) as $sword)
		
 
	} // end of 	foreach ($my_search_queries as $my_search_query){
		
// Sort the $results_array by DESC frequency value
				
				asort($results_array);
				
				$sorted_results = array_keys($results_array);
	
// $sorted_results has our results (products IDs) sorted by relevance

// now we can display them
			foreach ($sorted_results as $relevant_result) {
					
				$result = $mysqli->query('SELECT * FROM products WHERE ID = '.$relevant_result);
				$table  = mysqli_fetch_array($result,MYSQL_ASSOC);

				echo '<h2><a href="product.php?product_id='.$table['ID'].'">'.$table['product_title'].'</a></h2>';
				
				//I assume that you've created another table of all products full content.
				// Here in this example I created product.php as product full description page. 
				
				echo '<p>'.$table['product_description'].'</p>';
				
				
			}


} // end if (isset($_POST['search'])) 





//Some other functions

// connect function you can modify parameters or function
// you can modify this 

function connect () {
	$mysqli = new mysqli("localhost", "root", "", DATABASE);
if ($mysqli->connect_errno) {
    echo "Error : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}	

return $mysqli;
}

/* Check whether the synonym exists in your personal index of synonyms or not
* IT IS A SORT OF SYNONYMS FILTER MECHANISM
 * */
function word_exists($word) {
	
$mysqli = connect();
	$results = $mysqli ->query('SELECT word FROM words WHERE word="' . $word . '"');
	$table  = mysqli_fetch_array($results,MYSQL_ASSOC);
	
	if (count($table) < 1) {
		return false;
	} else {
		return true;
	}
}
?>
<h2>Search :
<form method="post" action="index.php" >
	<input required="true" name="search" style="width: 400px; font-size: 16px"  />
</form></h2>
