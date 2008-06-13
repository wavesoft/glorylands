<?php

// =============================================================
//                          (SAMPLE FILE)
// =============================================================
//  This file contains all the information tha are required
//  in order to process the action file quickly and efficiently.
//  This file contains:
//
//    1) The processing method of the file:
//        [1] Standard   : Execute all the process till end
//        [2] Quick      : Exclude some initialization parts
//        [3] Non-output : Exclude the output buffering and  
//                         processing parts
//    2) The default interface and output mode for the file
//    3) The include files required for this actions
//       (Use this instead of "include" or "require" directives
//        in order to handle a structured and single-include mode)
//    4) The helper classes this action uses
//    5) The classes might be created by input
//    6) Execution requirements. Theese are required in order to
//       deny execution of the function when or where it is not
//       supposed to be executed. For example, a MAP function
//       should only be executed if we are not in battle state
//       or somewere else..
//         [1] (Not implemented Yet, just scheduled)
// =============================================================

// [ From Event Processing ]

// forced_interface			 - Override interface from url
// default_interface		 - Default interface to use, if not defined
// forced_outmode			 - Override output mode from url
// default_outmode			 - Default output mode to use, if not defined
// post_processor			 - The name of the output post-processor to be used (if set)
// post_processor_unset_vars - The variables to unset from $act_result before passing it to post-processor

// [ From Action Processing ]

// managers[]				- A list of the managers that will be instanced when located in the URL. 
//							  If missing, every manager will be loaded and checked, if <false>, no managers will be loaded
// helpers[]				- A list of the helper classes that will be instanced on the global class variable chain
//						      and forwarded to the action. 
//							  If missing, every class named as helper and included by now will be instanced. If <false>m no
//							  helpers will be loaded.
// lib[]				    - A list of libraries that will be included from actions.lib directory. This happens before the
//							  helper initialization. That means a library can include a helper class.

// [ From Validity Processing ]

// user_cooldown[]			- User-specific cooldown id's that must be zero in order to proceed 
// global_cooldown[]		- Global-specific cooldown id's that must be zero in order to proceed
// time[]['lo','hi']		- The time range (in minutes from midnight) the action can be executed. If missing: always
// validade()				- A function that performs custom validation and returns true or false depending on 
//							  the result. True will allow execution, where false will stop it.

// Profile configuration for action : MAP

$pf = Array(

);

return $pf;
?>