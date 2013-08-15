<?php

function delete_path( $model ) 
{
    
    global $router;
    $str = "/".plurilize(  strtolower(get_class( $model )) )."/".$model->id."/delete";
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
    
}

function update_path( $model )
{
    global $router;
    $str = "/".  plurilize( strtolower(get_class( $model )) )."/".$model->id;
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function edit_path( $model )
{
    global $router;
    $str = "/".plurilize( strtolower(get_class( $model )) )."/".$model->id."/edit";
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function show_path( $model )
{
    global $router;
    $str = "/".plurilize( strtolower(get_class( $model )) )."/".$model->id;
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function index_path( $model )
{
    global $router;
    
    if(gettype($model) == "string") {
        $str = "/".plurilize( strtolower($model) );
    } else {
        $str = "/".plurilize( strtolower(get_class( $model )) );
    }
    
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function create_path( $model )
{
    global $router;
    if(gettype($model) == "string") {
        $str = "/".plurilize( strtolower($model) );
    } else {
        $str = "/".plurilize( strtolower(get_class( $model )) );
    }
    
    $str = "/".plurilize( strtolower(get_class( $model )) );
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function new_path( $model )
{
    global $router;
    
    if(gettype($model) == "string") {
        $str = "/".plurilize( strtolower($model) )."/new";
    } else {
        $str = "/".plurilize( strtolower(get_class( $model )) )."/new";
    }
    if(!empty($router->scope)) {
        $str = "/".$router->scope.$str;
    }
    return $str;
}

function make_link( $href, $text = null, $attributes = array() )
{
    
    $str = '<a href="'.trim($href).'"';
    
    foreach($attributes as $key => $value) 
    {
        $str .= ' '.$key.'="'.$value.'"';
    }
    
    return $str.'>'.trim($text).'</a>';
    
}

//Graphicly dump a variable
function examine( &$var )
{
    
    Libs\Rosborne\CFDump::dump($var);
    
}



function plurilize( $word ) 
{
    global $inflictor;
    return $inflictor->pluralize($word);
}

function singular( $word )
{
    global $inflictor;
    return $inflictor->singularize($word);
}