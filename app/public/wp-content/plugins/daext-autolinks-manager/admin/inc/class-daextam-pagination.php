<?php

/*
 * Handles the pagination on the back-end menus
 */

class Daextam_Pagination
{

    //PROPERTIES ---------------------------------------------------------------

    //Total number of items
    var $total_items = -1;

    //Number of records to display per page
    var $record_per_page = 10;

    //Target page url
    var $target_page = "";

    //Store the number of adjacent pages to show on each side of the current page inside the pagination
    var $adjacents = 2;

    //Store the current page value, this is set through the set_current_page() method
    var $current_page = 0;

    //Store the $_GET parameter to use
    var $parameterName = "p";

    //$calculate is set to True only when the calculate() method has been already executed
    var $calculate = false;

    //METHODS ------------------------------------------------------------------

    //Set the total number of items
    function set_total_items($value)
    {
        $this->total_items = (int)$value;
    }

    #Set the number of items to show per page
    function set_record_per_page($value)
    {
        $this->record_per_page = (int)$value;
    }

    #Set the page url where to send $this->parameterName
    function set_target_page($value)
    {
        $this->target_page = $value;
    }

    //Set the the current page parameter by getting it from $_GET['p'], if it's not set or it's not > than 0 then set it to 1
    function set_current_page()
    {

        if (isset($_GET[$this->parameterName])) {

	        $page_number = intval($_GET[$this->parameterName], 10);

            if ($page_number > 0 and $page_number <= ceil($this->total_items / $this->record_per_page)) {
                $this->current_page = $page_number;
            } else {
                $this->current_page = 1;
            }

        } else {

            $this->current_page = 1;

        }

    }

    //Set the number of adjacent pages to show on each side of the current page inside the pagination
    function adjacents($value)
    {
        $this->adjacents = (int)$value;
    }

    //set an alternative class name of the pagination div
    function changeClass($value = "")
    {
        $this->className = $value;
    }

    //assing a different $_GET parameter instead of p
    function parameterName($value = "")
    {
        $this->parameterName = $value;
    }

    //Calculate the pagination and display it
    function show()
    {

        //Generate the $pagination property with the $this->pagination inside of it
        $this->calculate();
        //Echo the $this->pagination property
        echo $this->pagination;

    }

    //return the complete url associated with this page id
    function get_pagenum_link($id)
    {

	    //search: s ----------------------------------------------------------------------------------------------------
	    if (isset($_GET['s']) and mb_strlen(trim($_GET['s'])) > 0) {
		    $filter = '&s=' . sanitize_text_field($_GET['s']);
	    } else {
		    $filter = '';
	    }

	    //custom filter (used in autolinks menu)
	    if (isset($_GET['cf']) and $_GET['cf'] != 'all') {
		    $filter .= '&cf=' . intval($_GET['cf'], 10);
	    }

	    if (strpos($this->target_page, '?') === false) {
		    return esc_url($this->target_page . "?" . $this->parameterName . "=" . $id . $filter);
	    } else {
		    return esc_url($this->target_page . "&" . $this->parameterName . "=" . $id . $filter);
	    }

    }

    //Generate the pagination and store it inside the $this->pagination property
    function calculate()
    {

        //Reset the $pagination variable, that store the resulted pagination
        $this->pagination = "";

        //Setup page vars for display
        $prev     = $this->current_page - 1;//previous page
        $next     = $this->current_page + 1;//next page
        $lastpage = ceil($this->total_items / $this->record_per_page);//last page        
        $lpm1     = $lastpage - 1;//last page minus 1

        //Generate the pagination and save it inside the $this->pagination property
        if ($lastpage > 1) {

            //Generate previous button
            if ($this->current_page) {
                if ($this->current_page > 1) //if the current page is > 1 the previous button is clickable
                {
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($prev) . '" class="prev">&#171</a>';
                } else //if the current page is not > 1 the previous button is not clickable
                {
                    $this->pagination .= '<a href="javascript: void(0)" class="disabled">&#171</a>';
                }
            }

            //Generate pages buttons
            if ($lastpage < 7 + ($this->adjacents * 2)) {

                //not enough pages to bother breaking it up
                for ($counter = 1; $counter <= $lastpage; $counter++) {
                    if ($counter == $this->current_page) {
                        $this->pagination .= '<a href="javascript: void(0)" class="disabled">' . $counter . '</a>';
                    } else {
                        $this->pagination .= '<a href="' . $this->get_pagenum_link($counter) . '">' . $counter . '</a>';
                    }
                }

            } elseif ($lastpage > 5 + ($this->adjacents * 2)) {//enough pages to hide some

                //close to beginning; only hide later pages
                if ($this->current_page < 1 + ($this->adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($this->adjacents * 2); $counter++) {
                        if ($counter == $this->current_page) {
                            $this->pagination .= '<a href="javascript: void(0)" class="disabled">' . $counter . '</a>';
                        } else {
                            $this->pagination .= '<a href="' . $this->get_pagenum_link($counter) . '">' . $counter . '</a>';
                        }
                    }
                    $this->pagination .= "<span>...</span>";
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($lpm1) . '">' . $lpm1 . '</a>';
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($lastpage) . '">' . $lastpage . '</a>';
                } //in middle; hide some front and some back
                elseif ($lastpage - ($this->adjacents * 2) > $this->current_page && $this->current_page > ($this->adjacents * 2)) {
                    $this->pagination .= '<a href="' . $this->get_pagenum_link(1) . '">1</a>';
                    $this->pagination .= '<a href="' . $this->get_pagenum_link(2) . '">2</a>';
                    $this->pagination .= "<span>...</span>";
                    for ($counter = $this->current_page - $this->adjacents; $counter <= $this->current_page + $this->adjacents; $counter++) {
                        if ($counter == $this->current_page) {
                            $this->pagination .= '<a href="javascript: void(0)" class="disabled">' . $counter . '</a>';
                        } else {
                            $this->pagination .= '<a href="' . $this->get_pagenum_link($counter) . '">' . $counter . '</a>';
                        }
                    }
                    $this->pagination .= "<span>...</span>";
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($lpm1) . '">' . $lpm1 . '</a>';
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($lastpage) . '">' . $lastpage . '</a>';
                } //close to end; only hide early pages
                else {
                    $this->pagination .= '<a href="' . $this->get_pagenum_link(1) . '">1</a>';
                    $this->pagination .= '<a href="' . $this->get_pagenum_link(2) . '">2</a>';
                    $this->pagination .= "<span>...</span>";
                    for ($counter = $lastpage - (2 + ($this->adjacents * 2)); $counter <= $lastpage; $counter++) {
                        if ($counter == $this->current_page) {
                            $this->pagination .= '<a href="javascript: void(0)" class="disabled">' . $counter . '</a>';
                        } else {
                            $this->pagination .= '<a href="' . $this->get_pagenum_link($counter) . '">' . $counter . '</a>';
                        }
                    }
                }
            }

            //Generate next button
            if ($this->current_page) {
                if ($this->current_page < $counter - 1) {
                    $this->pagination .= '<a href="' . $this->get_pagenum_link($next) . '" class="next">&#187</a>';
                } else {
                    $this->pagination .= '<a href="javascript: void(0)" class="disabled">&#187</a>';
                }
            }

        }

    }

    //generate the query string to use inside the SQL query
    function query_limit()
    {

        //calculate the $list_start position
        $list_start = ($this->current_page - 1) * $this->record_per_page;

        //Start of the list should be less than pagination count
        if ($list_start >= $this->total_items) {
            $list_start = ($this->total_items - $this->record_per_page);
        }

        //list start can't be negative
        if ($list_start < 0) {
            $list_start = 0;
        }

        return 'LIMIT ' . intval($list_start, 10) . ', ' . intval($this->record_per_page, 10);

    }

}