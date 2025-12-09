<?php
class STYLEMAPBIBTEX
{
	var $citation;
	var $types;
	var $basic;
	var $genericBook;
	var $genericArticle;
	var $genericMisc;
	var $book;
	var $book_article;
	var $journal_article;
	var $newspaper_article;
	var $magazine_article;
	var $proceedings;
	var $conference_paper;
	var $proceedings_article;
	var $thesis;
	var $web_article;
	var $film;
	var $broadcast;
	var $music_album;
	var $music_track;
	var $music_score;
	var $artwork;
	var $software;
	var $audiovisual;
	var $database;
	var $government_report;
	var $report;
	var $hearing;
	var $statute;
	var $legal_ruling;
	var $case;
	var $bill;
	var $patent;
	var $personal;
	var $unpublished;
	var $classical;
	var $manuscript;
	var $map;
	var $chart;
	var $miscellaneous;

	function __construct()
	{
		$this->loadMap();
		// Reverse order
		foreach ($this->types as $type => $bibtextype) {
			//print "Type: $type\n";
			if (!isset($this->$type)) {
				continue;
			}
			$tmp = $this->$type;
			if (is_array($tmp)) {
				$this->$type = array();
				//print_r($tmp);
				$a = &$this->$type;
				foreach ($tmp as $key => $value) {
					if (is_numeric($key)) continue;

					if (preg_match("/^creator/", $key)) {
						if ($value == "creator")
							$a[$value] = "author";
						else
							$a[$value] = $value;
					} else
						$a[$value] = $key;
				}
			}
			//print "Gives\n";
			//print_r($this->$type);
		}
	}
	/**

	 * loadMap: Load the map into arrays based on resource type.
	 *
	 * The basic() array contains database fields that are common to all types of resources.
	 * The key is the database field and the value is displayed to the user to be part of the style definition.
	 * e.g. if the user enters:
	 * author. title. publisherName|: publisherLocation|.
	 * for a style definition for a book, we know that 'author' is the database field 'creator1', 'title' is 
	 * the database field 'title' etc.
	 * There are some exceptions as defined by WIKINDX (other systems may have different methods).  Because these may be 
	 * represented in different ways in different systems, you will need to explicitly define these.  See BIBSTYLE.php 
	 * for examples of how WIKINDX does this.  The comments below relate to how WIKINDX stores such values in its database:
	 * 1/ 'originalPublicationYear doesn't exist in the database but is used to re-order publicationYear and reprintYear 
	 * for book and book_article resource types.
	 * 2/ 'pages' doesn't exist in the database but is created on the fly in BIBSTYLE.php as an amalgamation of 
	 * the database fields pageStart and pageEnd.
	 * 3/ 'date' doesn't exist in the database but is created on the fly in BIBSTYLE.php as an amalgamation of 
	 * the database fields miscField2 (day) and miscField3 (month).
	 * 4/ 'runningTime' doesn't exist in the database but is created on the fly in BIBSTYLE.php as an amalgamation of 
	 * the database fields miscField1 (minute) and miscField4 (hour) for film/broadcast.
	 *
	 * @author Mark Grimshaw
	 */
	function loadMap()
	{
		/**
		 * What fields are available to the in-text citation template? This array should NOT be changed.
		 * Currently, in-text citation formatting is not available (although it is defined in the XML style file). Future 
		 * releases will implement this.
		 */
		$this->citation = array(
			"creator" => "creator",
			"title"	=>	"title",
			"year" => "year",
			"pages" => "pages",
			"ID" => "ID"
		);
		/**
		 * NB NB NB NB NB NB NB NB NB NB NB
		 * 
		 * Map between OSBib's resource types (keys) and the bibliographic system's resource types (values). You must 
		 * NOT remove any elements or change the generic types. You may edit the value of each element. If your system 
		 * does not have a particular resource type, then you should set the value to FALSE (e.g. 'film' => FALSE,)
		 */
		$this->types = array(
			// The generic types must be present and unchanged.  DO NOT CHANGE THE VALUE OF THESE THREE!
			'genericBook'		=>	FALSE,
			'genericArticle'	=>	FALSE,
			'genericMisc'		=>	FALSE,
			// Edit values if necessary
			'book'			=>	'book',
			'book_article'		=>	array('inbook', 'incollection'),
			'journal_article'	=>	'article',
			'newspaper_article'	=>	FALSE,
			'magazine_article'	=>	FALSE,
			'proceedings'		=>	'proceedings',
			'conference_paper'	=>	FALSE,
			'proceedings_article'	=>	'inproceedings',
			'thesis'		=>	array(array('phdthesis', array("label" => "PhD Thesis")), array('mastersthesis', array("label" => "Master Thesis"))),
			'web_article'		=>	FALSE,
			'film'			=>	FALSE,
			'broadcast'		=>	FALSE,
			'music_album'		=>	FALSE,
			'music_track'		=>	FALSE,
			'music_score'		=>	FALSE,
			'artwork'		=>	FALSE,
			'software'		=>	FALSE,
			'audiovisual'		=>	FALSE,
			'database'		=>	FALSE,
			'government_report'	=>	FALSE,
			'report'		=>	'techreport',
			'hearing'		=>	FALSE,
			'statute'		=>	FALSE,
			'legal_ruling'		=>	FALSE,
			'case'			=>	FALSE,
			'bill'			=>	FALSE,
			'patent'		=>	FALSE,
			'personal'		=>	FALSE,
			'unpublished'		=>	'unpublished',
			'classical'		=>	FALSE,
			'manuscript'		=>	FALSE,
			'map'			=>	FALSE,
			'chart'			=>	FALSE,
			'miscellaneous'		=>	'misc',
		);
		/**
		 * Basic array of elements common to all types - change the key to map the database field that stores that value.
		 */
		$this->basic = array(
			'title'		=>	'title',
			'year'		=>	'publicationYear',
		);
		/**
		 * Creator mapping.  OSBib uses 'creator1' .. 'creator5' for internally managing creator names such as 
		 * author, editor, series editor, translator, reviser, artist, inventor, composer etc.  The associative 
		 * array (SQL row) you submit to $this->bibformat->preProcess() MUST use these fields for the creators.
		 * Furthermore, you may NOT change any keys (or values) in the arrays below that are 'creator1' ... 'creator5'.
		 */

		/**
		 * NB NB NB NB NB NB NB NB NB NB NB
		 *
		 * For the following arrays, the only things you should change are the keys of each array (except 'creator1' 
		 * .. 'creator5' - see above).  These keys are your database fieldnames for resources.
		 * The values are displayed to the user when creating/editing a style and 
		 * must NOT change or be removed.  If your database does not store a particular value, then it should still 
		 * exist in the array and must have a null key (e.g. $this->book[] = 'publisherName'; in the case of a database 
		 * that does not store publisher names for books ;-)).
		 * 
		 * The keys 'creator1', 'creator2', 'date' and 'URL' are special keys.  All other keys should be lowercase 
		 * field names from the BibTeX specification.
		 **************
		 **************
		 * Do NOT remove arrays.
		 * Do not remove array elements.
		 * Do not add array elements.
		 **************
		 **************
		 *
		 * You do not need to edit arrays where the value in $this->types above is FALSE as the array will then simply be 
		 * ignored.  So, although 34 resource types are defined here, if you system only has 6 resource types, you only need 
		 * to edit those 6 types.
		 *
		 * If you do not conform to this, OSBib XML style definition sheets you produce will not be compatible with other systems.
		 */
		// Three Generic fallback types used when there's no style definition for one of the resources below.
		// Generic Book type - no collection data, like a book
		$this->genericBook = $this->basic;
		$this->genericBook['creator1'] = 'creator';
		$this->genericBook['creator2'] = 'editor';
		$this->genericBook['publisher'] = 'publisherName';
		$this->genericBook['address'] = 'publisherLocation';
		$this->genericBook['ISBN'] = 'ID';
		// Generic Article type - in a collection like an article
		$this->genericArticle = $this->basic;
		$this->genericArticle['creator1'] = 'creator';
		$this->genericArticle['creator2'] = 'editor';
		$this->genericArticle['journal'] = 'collection';
		$this->genericArticle['publisher'] = 'publisherName';
		$this->genericArticle['address'] = 'publisherLocation';
		$this->genericArticle['date'] = 'date';
		$this->genericArticle['pages'] = 'pages';
		$this->genericArticle['ISBN'] = 'ID';
		// Generic Miscellaneous type - whatever is best not put in the above two fall back types....?
		$this->genericMisc = $this->basic;
		$this->genericMisc['creator1'] = 'creator';
		$this->genericMisc['publisher'] = 'publisherName';
		$this->genericMisc['address'] = 'publisherLocation';
		$this->genericMisc['type'] = 'type';
		$this->genericMisc['date'] = 'date';
		$this->genericMisc['ISBN'] = 'ID';

		// Resource specific mappings. The order here is the display order when editing/creating styles.
		// BOOK
		$this->book = $this->basic;
		$this->book['creator1'] = 'author';
		$this->book['creator2'] = 'editor';
		$this->book[] = 'translator';
		$this->book[] = 'reviser';
		$this->book[] = 'seriesEditor';
		$this->book['series'] = 'seriesTitle';
		$this->book['edition'] = 'edition';
		$this->book['number'] = 'seriesNumber';
		$this->book[] = 'numberOfVolumes';
		$this->book['volume'] = 'volumeNumber';
		$this->book[] = 'originalPublicationYear';
		$this->book[] = 'volumePublicationYear';
		$this->book['publisher'] = 'publisherName';
		$this->book['address'] = 'publisherLocation';
		$this->book['ISBN'] = 'ISBN';
		// BOOK ARTICLE/CHAPTER
		$this->book_article = $this->book;
		$this->book_article['bookitle'] = 'book';
		$this->book_article[] = 'shortBook';
		$this->book_article['pages'] = 'pages';
		// JOURNAL ARTICLE
		$this->journal_article = $this->basic;
		$this->journal_article['creator1'] = 'author';
		$this->journal_article['volume'] = 'volume';
		$this->journal_article['number'] = 'issue';
		$this->journal_article['journal'] = 'journal';
		$this->journal_article[] = 'shortJournal';
		$this->journal_article['pages'] = 'pages';
		$this->journal_article['ISSN'] = 'ISSN';
		// NEWSPAPER ARTICLE
		$this->newspaper_article = $this->basic;
		$this->newspaper_article['year'] = 'issueYear'; // override publicationYear
		$this->newspaper_article['date'] = 'issueDate';
		$this->newspaper_article['creator1'] = 'author';
		$this->newspaper_article['journal'] = 'newspaper';
		$this->newspaper_article[] = 'shortNewspaper';
		$this->newspaper_article['chapter'] = 'section';
		$this->newspaper_article['address'] = 'city';
		$this->newspaper_article['pages'] = 'pages';
		$this->newspaper_article['ISSN'] = 'ISSN';
		// MAGAZINE ARTICLE
		$this->magazine_article = $this->basic;
		$this->magazine_article['year'] = 'issueYear'; // override publicationYear
		$this->magazine_article['date'] = 'issueDate';
		$this->magazine_article['creator1'] = 'author';
		$this->magazine_article['journal'] = 'magazine';
		$this->magazine_article[] = 'shortMagazine';
		$this->magazine_article['edition'] = 'edition';
		$this->magazine_article['type'] = 'type';
		$this->magazine_article['volume'] = 'volume';
		$this->magazine_article['number'] = 'number';
		$this->magazine_article['pages'] = 'pages';
		$this->magazine_article['ISSN'] = 'ISSN';
		// PROCEEDINGS ARTICLE
		$this->proceedings_article = $this->basic;
		$this->proceedings_article['creator1'] = 'author';
		$this->proceedings_article['booktitle'] = 'conference';
		$this->proceedings_article[] = 'shortConference';
		$this->proceedings_article['organization'] = 'conferenceOrganiser';
		$this->proceedings_article['address'] = 'conferenceLocation';
		$this->proceedings_article['date'] = 'conferenceDate';
		// overwrite publicationYear
		$this->proceedings_article['year'] = 'conferenceYear';
		$this->proceedings_article['pages'] = 'pages';
		$this->proceedings_article['ISBN'] = 'ISSN';
		// THESIS
		$this->thesis = $this->basic;
		// overwrite publicationYear
		$this->thesis['year'] = 'awardYear';
		$this->thesis['creator1'] = 'author';
		$this->thesis[] = 'label'; // 'thesis', 'dissertation'
		// 'type' is special and used in BIBFORMAT.php
		$this->thesis['type'] = 'type'; // 'Master's', 'PhD', 'Doctoral', 'Diploma' etc.
		$this->thesis['institution'] = 'institution';
		$this->thesis['address'] = 'institutionLocation';
		$this->thesis[] = 'department';
		$this->thesis['journal'] = 'journal';
		$this->thesis[] = 'shortJournal';
		$this->thesis['volume'] = 'volumeNumber';
		$this->thesis['number'] = 'issueNumber';
		$this->thesis['pages'] = 'pages';
		// REPORT
		$this->report = $this->basic;
		$this->report['creator1'] = 'author';
		$this->report['institution'] = 'institution';
		$this->report['address'] = 'institutionLocation';
		$this->report['type'] = 'type';
		$this->report['number'] = 'reportNumber';
		$this->report['date'] = 'date';
		$this->report['pages'] = 'pages';
		// UNPUBLISHED
		$this->unpublished = $this->basic;
		$this->unpublished['creator1'] = 'author';
		$this->unpublished['date'] = 'date';
		$this->unpublished['pages'] = 'pages';
		// PROCEEDINGS
		$this->proceedings = $this->basic;
		$this->proceedings['creator1'] = 'editor';
		$this->proceedings['organization'] = 'conferenceOrganiser';
		$this->proceedings['address'] = 'conferenceLocation';
		$this->proceedings['date'] = 'conferenceDate';
		$this->proceedings['publisher'] = 'publisherName';
		$this->proceedings['volume'] = 'volume';
		$this->proceedings['series'] = 'seriesTitle';
		$this->proceedings['ISBN'] = 'ISBN';
		// MISCELLANEOUS
		$this->miscellaneous = $this->basic;
		$this->miscellaneous['creator1'] = 'author';
		$this->miscellaneous['creator2'] = 'editor';
		$this->miscellaneous['publisher'] = 'publisherName';
		$this->miscellaneous['address'] = 'publisherLocation';
		$this->miscellaneous['type'] = 'type';
		$this->miscellaneous['date'] = 'date';
		$this->miscellaneous['pages'] = 'pages';
		$this->miscellaneous['ISBN'] = 'ID';
		$this->miscellaneous['howpublished'] = 'howpublished';
	}
}
