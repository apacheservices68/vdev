<?php
use \PDO;
use Illuminate\Database\Schema;
use Illuminate\Support\Str;
use Illuminate\Session;
/*
 * Helper functions for building a DataTables server-side processing SQL query
 *
 * The static functions in this class are just helper functions to help build
 * the SQL used in the DataTables demo server-side processing scripts. These
 * functions obviously do not represent all that can be done with server-side
 * processing, they are intentionally simple to show how it works. More complex
 * server-side processing operations will likely require a custom script.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */


// REMOVE THIS BLOCK - used for DataTables test environment only!
// $file = $_SERVER['DOCUMENT_ROOT'].'/datatables/mysql.php';
// if ( is_file( $file ) ) {
//     include( $file );
// }


class datatable2 {

    /**
     * Create the data output array for the DataTables rows
     *
     * @param array $columns Column information array
     * @param array $data    Data from the SQL get
     * @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     * @return array Formatted data in a row based format
     */

    

    static function data_output( $columns, $data, $isJoin = false,$table = NULL )
    {
        $out = array();
        $array_session = array();
        for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
            $row = array();
            $id = $group = null;
            for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
                $column = $columns[$j];
              
                if($j == 0){
                    $id = $data[$i][ $columns[$j]['field'] ];
                }
                // Is there a formatter?
                if ( isset( $column['formatter'] ) ) {
                    $row[ $column['dt'] ] = ($isJoin) ? $column['formatter']( $data[$i][ $column['field'] ], $data[$i] ) : $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
                }
                else {
                    $row[ $column['dt'] ] = ($isJoin) ? $data[$i][ $columns[$j]['field'] ] : $data[$i][ $columns[$j]['db'] ];
                }
            }
            $array_session[] = $row;
            $m = count($row);
            if($table == 'users' ){
                if($id == \Session::get('bsd_group_id')){
                    $row[$m]   = "<input type='checkbox' class='icheckbox_square' disabled='' name='checkboxes[]' value='{$id}' />";    
                }else{
                    $row[$m]   = "<input type='checkbox' class='icheckbox_square' name='checkboxes[]' value='{$id}' />";    
                }
            }else{
                $row[$m]   = "<input type='checkbox' class='icheckbox_square' name='checkboxes[]' value='{$id}' />";    
            }
            
            $row[$m+1] = "<a href='javascript:void(0)' onclick='redirect(\"view\",\"{$id}\")' class='btn btn-mini btn-default'><span class='glyphicon glyphicon-eye-open'></span></a>
            <a class='btn btn-mini btn-primary' onclick='redirect(\"edit\",\"{$id}\")' bsd-action='edit' bsd-id='{$id}' href='javascript:void(0)' ><i class='fa fa-pencil-square-o'></i></a>";
            $m = 0; $id = null;
            $out[] = $row;
        }
        \Session::put('dashboard_export_sess',$array_session);
        return $out;
    }

    
    /**
     * Paging
     *
     * Construct the LIMIT clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @return string SQL limit clause
     */
    static function limit ( $request, $columns )
    {
        $limit = '';

        if ( isset($request['start']) && $request['length'] != -1 ) {
            $limit = "LIMIT ".intval($request['start']).", ".intval($request['length']);
        }

        return $limit;
    }


    /**
     * Ordering
     *
     * Construct the ORDER BY clause for server-side processing SQL query
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL order by clause
     */
    static function order ( $request, $columns, $isJoin = false )
    {
        $order = '';

        if ( isset($request['order']) && count($request['order']) ) {
            $orderBy = array();
            $dtColumns = datatable2::pluck( $columns, 'dt' );

            for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
                // Convert the column index into the column data property
                $columnIdx = intval($request['order'][$i]['column']);
                $requestColumn = $request['columns'][$columnIdx];

                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['orderable'] == 'true' ) {
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';

                    $orderBy[] = ($isJoin) ? $column['db'].' '.$dir : '`'.$column['db'].'` '.$dir;
                }
            }

            $order = 'ORDER BY '.implode(', ', $orderBy);
        }

        return $order;
    }


    /**
     * Searching / Filtering
     *
     * Construct the WHERE clause for server-side processing SQL query.
     *
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here performance on large
     * databases would be very poor
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $columns Column information array
     *  @param  array $bindings Array of values for PDO bindings, used in the sql_exec() function
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *
     *  @return string SQL where clause
     */
    static function filter ( $request, $columns, &$bindings, $isJoin = false )
    {
        $globalSearch = array();
        $columnSearch = array();
        $dtColumns = datatable2::pluck( $columns, 'dt' );

        if ( isset($request['search']) && $request['search']['value'] != '' ) {
            $str = $request['search']['value'];

            for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
                $requestColumn = $request['columns'][$i];
                $columnIdx = array_search( $requestColumn['data'], $dtColumns );
                $column = $columns[ $columnIdx ];

                if ( $requestColumn['searchable'] == 'true' ) {
                    $binding = datatable2::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                    $globalSearch[] = ($isJoin) ? $column['db']." LIKE ".$binding : "`".$column['db']."` LIKE ".$binding;
                }
            }
        }

        // Individual column filtering
        for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
            $requestColumn = $request['columns'][$i];
            $columnIdx = array_search( $requestColumn['data'], $dtColumns );
            $column = $columns[ $columnIdx ];

            $str = $requestColumn['search']['value'];

            if ( $requestColumn['searchable'] == 'true' &&
                $str != '' ) {
                $binding = datatable2::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                $columnSearch[] = ($isJoin) ? $column['db']." LIKE ".$binding : "`".$column['db']."` LIKE ".$binding;
            }
        }

        // Combine the filters into a single string
        $where = '';

        if ( count( $globalSearch ) ) {
            $where = '('.implode(' OR ', $globalSearch).')';
        }

        if ( count( $columnSearch ) ) {
            $where = $where === '' ?
                implode(' AND ', $columnSearch) :
                $where .' AND '. implode(' AND ', $columnSearch);
        }

        if ( $where !== '' ) {
            $where = 'WHERE '.$where;
        }

        return $where;
    }


    /**
     * Perform the SQL queries needed for an server-side processing requested,
     * utilising the helper functions of this class, limit(), order() and
     * filter() among others. The returned array is ready to be encoded as JSON
     * in response to an datatable2 request, or can be modified if needed before
     * sending back to the client.
     *
     *  @param  array $request Data sent to server by DataTables
     *  @param  array $sql_details SQL connection details - see sql_connect()
     *  @param  string $table SQL table to query
     *  @param  string $primaryKey Primary key of the table
     *  @param  array $columns Column information array
     *  @param  array $joinQuery Join query String
     *  @param  string $extraWhere Where query String
     *
     *  @return array  Server-side processing response array
     *
     */
    
    /*
    static function join()  {

    }

    static function columns($table)   {
        return \Schema::getColumnListing($table);
    }
    */
    static function columns($col = array()){
        $return = $new_array = $beta = array();
        $alpha = array('b','c','d','e','f');        
        $i = $j = 0 ;
        # Get list col
        $list_col = \Schema::getColumnListing($col['table']);
        if($col['default'] == true){
            # If default col define return ;
            foreach($list_col as $item){
                # Neu co split di col nao thi thuc hien action split 
                if(isset($col['split']) && $col['split'] != NULL)   {
                    if(in_array($item, $col['split'])){
                        continue;
                    }
                }  
                $return[] = array('db'=>$item,'dt'=>$i,'field' => $item );
                $i += 1;
            }        

            return $return ;
        }else{
            # Xu ly array slug name             
            foreach($col['join'] as $item){
                $beta[$item] = $alpha[$j];
                $j += 1;
            }

            # If isset join field and extra action      
            $j = 0;      
            foreach($list_col as $item){
                # Neu co split di col nao thi thuc hien action split 
                if(isset($col['split']) && $col['split'] != NULL)   {
                    if(in_array($item, $col['split'])){
                        continue;
                    }
                }
                $return[] = array('db'=>'`a`.`'.$item.'`','dt'=>$j,'field' => $item ,'as'=> $item);
                $new_array[$item] = $j;
                $j +=1;
            }
            # Neu action co them du col thi add them col 
            if(isset($col['add']) && $col['add'] != NULL){
                foreach($col['join'] as $item){
                    foreach($col['add'][$item] as $items){
                        $return[] = array('db'=>"`{$beta[$item]}`.`".$items.'`','dt'=>$j,'field' => $items ,'as'=> $item.$items);
                    }
                    $j += 1;
                }
            }       
            $i = 0;            
            # Neu action co thay doi du lieu cua col thi thuc hien action 
            if( isset($col['change']) && $col['change'] != NULL){
                foreach($col['change'] as $k => $v){  
                    $temp  = explode(".",$v);
                    $offset = $new_array[$k];
                    foreach($return[$offset] as $k => &$item){
                        if($k == 'db'){
                            $item = "`{$beta[$temp[0]]}`.`".$temp[1].'`';
                        }
                    }
                    #$return[$offset] = array('db'=>'`{$beta[$temp[0]]}`.`'.$temp[1].'`','dt'=>$i,'field' => $items ,'as'=> $item."-".$items);
                    $i += 1;
                }
            }
                
            
        }
        return $return ;
    }

    

    static function simple ( $request, $sql_details, $table, $primaryKey, $col, $join_table = NULL, $extraWhere = '', $groupBy = '',$many = false , $additional = NULL)
    {
        # $add = array('table'=>$table,'join'=>array('locations'),'default'=>false,'split'=>array(''),'add'=>array('locations'=>array('type')),'change'=>array('location_id'=>'locations.name'));
        # Fetch column if not
        /*
        if($columns == '' || $columns == NULL){
            $columns = datatable2::columns();
        }
        #$add = array('table'=>$table,'join'=>array('products'),'default'=>false,'split'=>array('updated_at','used','used_at','fetched_at'),'add'=>NULL,'change'=>array('product_id'=>'products.name'));
        */
        $columns = datatable2::columns($col);
        $string = '';
        $alpha = array('b','c','d','e','f');
        $i = 0 ;
        if($join_table != NULL){
            $string = "FROM `{$table}` AS `a` ";
            if(is_array($join_table)){
                foreach($join_table as $k => $v){
                    if($many == false){
                        if($additional != NULL){                            
                            $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`{$k}` = `a`.`{$additional[$v]}`) ";
                        }else{
                            $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`id` = `a`.`{$k}`) ";    
                        }                        
                    }else{
                        # Join many to many
                        if($i == 0){
                            if($additional != NULL && isset($additional[$v])){                            
                                #$string .= " JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`$k` = `a`.`$additional[$v]`) ";
                                $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`{$k}` = `a`.`{$additional[$v]}`) ";
                            }else{
                                $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`id` = `a`.`{$k}`) ";
                            }
                        }else{
                            /*$keygen = $i-1;
                            $old_table = $alpha[$keygen];
                            if((int)$keygen === 0){*/
                                $old_table = 'a';
                            #}
                            if($additional != NULL && isset($additional[$v])){
                                $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`{$k}` = `{$old_table}`.`{$additional[$v]}`) ";
                            }else{
                                $string .= "LEFT JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`id` = `{$old_table}`.`{$k}`) ";
                            }
                            #$string .= " JOIN `{$v}` AS `{$alpha[$i]}` ON (`{$alpha[$i]}`.`id` = `{$old_table}`.`{$k}`) ";
                        }
                        
                    }                    
                    $i += 1;
                }
            }
        }        
        unset($i);
        
        $joinQuery = $string ;
        $_new_array = array();
        foreach($columns as $k => $v){
            $temp  = str_replace("`","",$v);
            $_new_array[] = str_replace(".","-",$temp);
        }
        \Session::put("dashboard_export_sess_header",$_new_array);

        #unset ( $columns[ count($columns) ] ); 
        #unset ( $columns[ count($columns)+1 ] );

        $bindings = array();
        $db = datatable2::sql_connect( $sql_details );

        // Build the SQL query string from the request
        $limit = datatable2::limit( $request, $columns );
        $order = datatable2::order( $request, $columns, $joinQuery );
        $where = datatable2::filter( $request, $columns, $bindings, $joinQuery );

        // IF Extra where set then set and prepare query
        if($extraWhere)
            $extraWhere = ($where) ? ' AND '.$extraWhere : ' WHERE '.$extraWhere;

        // Main query to actually get the data
        if($joinQuery){
            $col = datatable2::pluck($columns, 'db', $joinQuery);
            $query =  "SELECT SQL_CALC_FOUND_ROWS ".implode(", ", $col)."
             $joinQuery
             $where
             $extraWhere
             $groupBy
             $order
             $limit";
        }else{
            $query =  "SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", datatable2::pluck($columns, 'db'))."`
             FROM `$table`
             $where
             $extraWhere
             $groupBy
             $order
             $limit";
        }
        
        $query = preg_replace("/\s{3,}/", " ", $query);
        $data = datatable2::sql_exec( $db, $bindings,$query);
        // Data set length after filtering
        $resFilterLength = datatable2::sql_exec( $db,
            "SELECT FOUND_ROWS()"
        );
        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        $resTotalLength = datatable2::sql_exec( $db,
            "SELECT COUNT(`{$primaryKey}`)
             FROM   `$table`"
        );
        $recordsTotal = $resTotalLength[0][0];
        /*
         * Output
         */
        return array(
            "draw"            => intval( $request['draw'] ),
            "recordsTotal"    => intval( $recordsTotal ),
            "recordsFiltered" => intval( $recordsFiltered ),
            "data"            => datatable2::data_output( $columns, $data, $joinQuery ,$table),
            #"queries"         => $query,
            #'new'             => $new_columns
        );
    }


    /**
     * Connect to the database
     *
     * @param  array $sql_details SQL server connection details array, with the
     *   properties:
     *     * host - host name
     *     * db   - database name
     *     * user - user name
     *     * pass - user password
     * @return resource Database connection handle
     */
    static function sql_connect ( $sql_details )
    {
        $user       = \Config::get('database.connections.mysql.username');
        $password   = \Config::get('database.connections.mysql.password');
        $dbs         = \Config::get('database.connections.mysql.database');
        $server     = \Config::get('database.connections.mysql.host');
        try {
            $db = new PDO(
                "mysql:host={$server};dbname={$dbs}",
                $user,
                $password,
                array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
            );
            $db->exec("set names utf8");
        }
        catch (PDOException $e) {
            self::fatal(
                "An error occurred while connecting to the database. ".
                "The error reported by the server was: ".$e->getMessage()
            );
        }

        return $db;
    }


    /**
     * Execute an SQL query on the database
     *
     * @param  resource $db  Database handler
     * @param  array    $bindings Array of PDO binding values from bind() to be
     *   used for safely escaping strings. Note that this can be given as the
     *   SQL query string if no bindings are required.
     * @param  string   $sql SQL query to execute.
     * @return array         Result from the query (all rows)
     */
    static function sql_exec ( $db, $bindings, $sql=null )
    {
        // Argument shifting
        if ( $sql === null ) {
            $sql = $bindings;
        }
        
        $stmt = $db->prepare( $sql );
        //echo $sql;

        // Bind parameters
        if ( is_array( $bindings ) ) {
            for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
                $binding = $bindings[$i];
                $stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
            }
        }
        // Execute
        try {
            $stmt->execute();
        }
        catch (PDOException $e) {
            datatable2::fatal( "An SQL error occurred: ".$e->getMessage() );
        }

        // Return all
        return $stmt->fetchAll();
    }


    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Internal methods
     */

    /**
     * Throw a fatal error.
     *
     * This writes out an error message in a JSON string which DataTables will
     * see and show to the user in the browser.
     *
     * @param  string $msg Message to send to the client
     */
    static function fatal ( $msg )
    {
        echo json_encode( array(
            "error" => $msg
        ) );

        exit(0);
    }

    /**
     * Create a PDO binding key which can be used for escaping variables safely
     * when executing a query with sql_exec()
     *
     * @param  array &$a    Array of bindings
     * @param  *      $val  Value to bind
     * @param  int    $type PDO field type
     * @return string       Bound key to be used in the SQL where this parameter
     *   would be used.
     */
    static function bind ( &$a, $val, $type )
    {
        $key = ':binding_'.count( $a );

        $a[] = array(
            'key' => $key,
            'val' => $val,
            'type' => $type
        );

        return $key;
    }


    /**
     * Pull a particular property from each assoc. array in a numeric array,
     * returning and array of the property values from each item.
     *
     *  @param  array  $a    Array to get data from
     *  @param  string $prop Property to read
     *  @param  bool  $isJoin  Determine the the JOIN/complex query or simple one
     *  @return array        Array of property values
     */
    static function pluck ( $a, $prop, $isJoin = false )
    {
        $out = array();

        for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            $out[] = ($isJoin && isset($a[$i]['as'])) ? $a[$i][$prop]. ' AS '.$a[$i]['as'] : $a[$i][$prop];
        }

        return $out;
    }
}