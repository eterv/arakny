<?php

/**
 * File System Helper
 * 파일 시스템 도우미
 *
 * @author      Lucas Choi <eterv@naver.com>
 * @link        http://arakny.com
 * @package     Arakny
 */

/* -------------------------------------------------------------------------------- */

function getAllSubDirectories( $directory, $directory_seperator )
{
    $dirs = array_map( function($item) use($directory_seperator){ return $item . $directory_seperator; }, array_filter( glob( $directory . '*' ), 'is_dir') );
    foreach( $dirs AS $dir ) {
        $dirs = array_merge( $dirs, getAllSubDirectories( $dir, $directory_seperator ) );
    }
    return $dirs;
}

/* -------------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------------- */