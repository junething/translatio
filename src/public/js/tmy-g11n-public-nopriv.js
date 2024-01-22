(function( $ ) {

    'use strict';
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

    jQuery(document).ready(function($){

        function findAndPrintTextNodes(node, sentencelist) {
            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            //if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
              if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '' && 
                  !$(node).closest('#wpadminbar').length && 
                  !$(node).closest('input, textarea').length &&
                  !$(node).closest('script').length &&
                  !$(node).closest('.translatio-logo-popup').length) {

                //console.log(node.nodeValue);
                var textContent = node.nodeValue.trim();
                textContent = textContent.replace(/^\n+|\n+$/g, '');

                sentencelist.push(textContent);
                if (textContent in translationTable) {
                    //console.log("         ");
                    //console.log("[" + textContent +"]");
                    //console.log("Translation Table: " + JSON.stringify(translationTable[textContent]));

                    //if (contentLanguage in translationTable[textContent]){
                        //console.log("Translation Table: " + translationTable[textContent][contentLanguage]);
                        //console.log("Translation Table: " + JSON.stringify(translationTable[textContent][contentLanguage]));
                        //textContent = translationTable[textContent][contentLanguage];
                        textContent = translationTable[textContent];
                    //}
                }
                // Highlight the text node with a border
                const span = document.createElement('span');
                span.className = 'tmyhighlighted';
                const highlightedText = document.createTextNode(textContent);
                span.appendChild(highlightedText);
                node.parentNode.replaceChild(span, node);

                //$(node.parentNode).on('mouseover', handleMouseOver);

                // Attach the mouseover and mouseout event handlers to the highlighted text nodes
                //$('.highlighted-text').on('mouseover', handleMouseOver).on('mouseout', handleMouseOut);

            } else {
                for (const childNode of node.childNodes) {
                  findAndPrintTextNodes(childNode, sentencelist);
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
                async:   false,
                url:     tmy_g11n_ajax_obj.ajax_url,
                data:    data,
                success: function(response) {
                   returnArr = JSON.parse(response);
                   translationTable = returnArr["return_data"];
                   returnMessage = returnArr["return_message"];
        
                   console.log(returnMessage);
                   console.log("translationTable");
                   console.log(translationTable);
       
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                }
            });
        }

        console.log("Starting TMY no priv");

        var translationTable;
        var sentencelist = [];
        handleBuildTranslationTable();
        findAndPrintTextNodes(document.body, sentencelist);

    }); //jquery ready

})( jQuery );
