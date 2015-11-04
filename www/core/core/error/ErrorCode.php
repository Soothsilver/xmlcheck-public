<?php

namespace asm\core;

/**
 * Contains predefined error code constants for unique identification of known error causes.
 */
class ErrorCode
{
	const unknown				=	0;		///< unknown cause
	
	const lowPrivileges		=	1;		///< user is not permitted to perform requested action
	const corruptedData		=	2;		///< uploaded data got corrupted probably on server side
	const mail					=	3;		///< sending of an e-mail failed
	const dbRequest			=	4;		///< database request failed
	const dbNameDuplicate	=	5;		///< database request failed because requested item name is already used
	const dbEmptyResult		=	6;		///< database request yielded no data when some were needed
	//const fileDuplicate		=	7;		///< user tried to upload file with name that is already used
	const zip					=	8;		///< cannot extract data from ZIP archive
	const inputInvalid		=	9;		///< UI request arguments don't fit required constraints (length, characters used, ...)
	const inputIncomplete	=	10;	///< some required UI request arguments are missing
	const upload				=	11;	///< file upload failed
	const pluginLaunch		=	12;	///< plugin launch failed
	const removeFile			=	13;	///< file removal failed
	const removeFolder		=	14;	///< folder removal failed
	const createFolder		=	15;	///< folder creation failed

    const sessionInvalid = 16;

	const special				=	99;	///< cause with no assigned error code
}

