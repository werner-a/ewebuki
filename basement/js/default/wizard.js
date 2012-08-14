    function preview(aktion) {
        
        jQuery.ajax({
            type: 'POST',
            url: aktion,
            data:'ajax=on&save=on&send=on&'+jQuery('#cEd').serialize(),
            success: function(data) {
                jQuery('#substance').html(data)
		
                jQuery("a[rel^='lightbox-preview']").slimbox(),
                jQuery("a[rel^='lightbox-image']").slimbox(),
        
                jQuery('dl').accordion({active:false,collapsible: true});
                jQuery('#preview_dialog').dialog({
                    title: 'Vorschau',
                    modal: true,
                    width:600,
                    close: function() {                                                                
                        jQuery('#substance').empty()
                    }
                })        
            }

        })                       
    }
