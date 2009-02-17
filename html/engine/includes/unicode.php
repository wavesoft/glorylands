<?php
/**
  * <h3>UNICODE UTF-8 Support</h3>
  *
  * This file contains all the functions needed to handle UTF-8
  * encoded UNICODE strings.
  *
  * @package GloryLands
  * @subpackage Engine
  * @author John Haralampidis <johnys2@gmail.com>
  * @copyright Copyright (C) 2007-2008, John Haralampidis
  * @version 1.0
  */

/**
  * Capitalize the first character of a string (same as ucfirst)
  *  
  * This function is a multibyte-safe implemention of the PHP's ucfirst
  *
  * @param	$str	string	The source string
  * @param	$enc	string	(Optional) The encoding to use
  *	@return	string			The result string
  *
  */
function gl_ucfirst($str, $enc = null)
{
  if($enc === null) $enc = mb_internal_encoding();
  return mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc).mb_substr($str, 1, mb_strlen($str, $enc), $enc);
}

?>