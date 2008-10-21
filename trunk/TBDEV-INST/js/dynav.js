function one2two() {
    var m1 = document.settings.options;
    var m2 = document.settings.dynavopt;
    
    m1len = m1.length ;
    for ( i=0; i<m1len ; i++){
        if (m1.options[i].selected == true ) {
            m2len = m2.length;
            m2.options[m2len]= new Option(m1.options[i].text,m1.options[i].value);
        }
    }

    for ( i = (m1len -1); i>=0; i--){
        if (m1.options[i].selected == true ) {
            m1.options[i] = null;
        }
    }
}

function two2one() {
    var m1 = document.settings.options;
    var m2 = document.settings.dynavopt;
    
    m2len = m2.length ;
        for ( i=0; i<m2len ; i++){
            if (m2.options[i].selected == true ) {
                m1len = m1.length;
                m1.options[m1len]= new Option(m2.options[i].text,m2.options[i].value);
            }
        }
        for ( i=(m2len-1); i>=0; i--) {
            if (m2.options[i].selected == true ) {
                m2.options[i] = null;
            }
        }
}

function selectall(obj) {
	   obj = (typeof obj == "string") ? document.getElementById(obj) : obj;
	   if (obj.tagName.toLowerCase() != "select")
		    return;
	   for (var i=0; i<obj.length; i++) {
		    obj[i].selected = true;
	 }
}
