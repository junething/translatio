(function( $ ) {

    'use strict';
    jQuery(document).ready(function($){

        function tmy_hbs_fc() {
            //console.log("hubspot form loaded" );

            const allIframes = $('iframe');
            allIframes.each(function(index, iframe) {
                const iframeContents = $(iframe).contents();
                const textNodes = iframeContents.find(':not(iframe, script, style)').contents().filter(function () {
                    return this.nodeType === 3 && this.nodeValue.trim() !== '';
                });
                textNodes.each(function (index, textNode) {
                    var textContent = textNode.nodeValue.trim();
                    textContent = textContent.replace(/^\n+|\n+$/g, '');
                    if (textContent in translationTable) {
                        textNode.nodeValue = translationTable[textContent];
                    }
                });
            });
        }
        if (window.addEventListener) {
            window.addEventListener("message", tmyMessageListener, false);        
        } else if (window.attachEvent) {
            window.attachEvent("message", tmyMessageListener, false);
        }
        function tmyMessageListener(event) {
            var data = event.data;      
            if (data.message === "tmy_hbs_fc") {
                if (typeof tmy_hbs_fc === 'function') {
                    tmy_hbs_fc(); 
                }
            }
        }

        function findAndPrintTextNodes(node) {
            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
              if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '' && 
                  !$(node).closest('#wpadminbar').length && 
                  !$(node).closest('input, textarea').length &&
                  !$(node).closest('script').length &&
                  !$(node).closest('.popup-container').length &&
                  !$(node).closest('.translatio-logo-popup').length) {
                var textContent = node.nodeValue.trim();
                textContent = textContent.replace(/^\n+|\n+$/g, '');
                if (textContent in translationTable) {
                    node.nodeValue = translationTable[textContent];
                }
            } else {
                for (const childNode of node.childNodes) {
                  findAndPrintTextNodes(childNode);
                }
            }
        }

        function handleBuildTranslationTable() {
            var returnArr;
            var returnMessage;
            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            var referenceId = $('meta[http-equiv="translatio-tmy-ref-id"]').attr('content');
            var data = {
                'action': 'tmy_g11n_frontend_jquery_call',
                'operation': 'tmy_ops_get_translation_table',
                'language': contentLanguage,
                'referenceid': referenceId
            };
            $.ajax({
                type:    "POST",
                url:     tmy_g11n_ajax_obj.ajax_url,
                data:    data,
                success: function(response) {
                   returnArr = JSON.parse(response);
                   translationTable = returnArr["return_data"];
                   returnMessage = returnArr["return_message"];
                   isDefaultLanguage = returnArr["return_is_default_lang"];
                   findAndPrintTextNodes(document.body);
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                }
            });
        }

        console.log("tmy front 3");
        var translationTable = {};
        var isDefaultLanguage;
        handleBuildTranslationTable();

    }); //jquery ready

})( jQuery );
