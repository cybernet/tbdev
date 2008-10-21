//\/////
//\  overLIB Crossframe Support Plugin
//\  This file requires overLIB 4.00 or later.
//\
//\  overLIB 4.00 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision: 1.3.4.1 $                $Date: 2004/03/23 16:29:38 $
//\/////


////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || olInfo.simpleversion < 400) alert('overLIB 4.00 or later is required for the Cross Frame Support Plugin.');
registerCommands('frame');


////////
//  PLUGIN FUNCTIONS
///////

// Parses FRAME command
function parseFrameExtras(pf,i,ar) {
	var k = i,v;

	if (k < ar.length) {
		if (ar[k] == FRAME) { v = ar[++k]; if(pf == 'ol_'&&compatibleframe(v)) ol_frame = v; else opt_FRAME(v); return k; }
	}

	return -1;
}

////////
// SUPPORT FUNCTIONS
////////

// Defines which frame we should point to.
function opt_FRAME(frm) {
 	o3_frame = compatibleFrame(frm) ? frm : ol_frame;
 	
	if (olNs4) {
		over = o3_frame.document.layers['overDiv'];
	} else if (document.all) {
		over = o3_frame.document.all['overDiv'];
	} else if (document.getElementById) {
		over = o3_frame.document.getElementById("overDiv");
	}

	return 0;
}

// Makes sure target frame has overLIB
function compatibleFrame(frameid) { 
	if (olNs4 && typeof frameid.document.overDiv == 'undefined') {
		return false;
	} else if (document.all && typeof frameid.document.all["overDiv"] == 'undefined') {
		return false;
	} else if (document.getElementById && frameid.document.getElementById('overDiv') == null) {
		return false;
	}

	return true;
}

// Get frame depth of nested frames
function frmDepth(thisFrame,ofrm) {
	var retVal = '';

	for (var i = 0; i<thisFrame.length; i++) {
		if (thisFrame[i].length > 0) { 
			retVal = frmDepth(thisFrame[i],ofrm);
			if (retVal ==  '') continue;
		} else if (thisFrame[i] != ofrm) continue;
		retVal = '[' + i + ']' + retVal;
		break;
	}

	return retVal;
}

// Gets frame reference value relative to top frame
function getFrmRef(srcFrm,tgetFrm) {
	var rtnVal = ''

	if (tgetFrm != srcFrm) {
		var tFrm = frmDepth(top.frames,tgetFrm)
		var sFrm = frmDepth(top.frames,srcFrm)
		if (sFrm.length ==  tFrm.length) {
			l = tFrm.lastIndexOf('[')
			
			if (l) {
				while ( sFrm.substring(0,l) != tFrm.substring(0,l) )
				l = tFrm.lastIndexOf('[',l-1)
				tFrm = tFrm.substr(l)
				sFrm = sFrm.substr(l)
			}
		}
	
		var cnt = 0, p = '',str = tFrm
		while ((k = str.lastIndexOf('[')) != -1) {
			cnt++ 
			str = str.substring(0,k)
		}

		for (var i = 0; i<cnt; i++) p = p + 'parent.'
		rtnVal = p + 'frames' + sFrm + '.'
	}
 
	return rtnVal
}

function chkForFrmRef() {
	if(o3_frame != ol_frame) fnRef = getFrmRef(ol_frame,o3_frame)
	return true;
}

////////
// PLUGIN REGISTRATIONS
////////
registerCmdLineFunction(parseFrameExtras);
registerPostParseFunction(chkForFrmRef);

