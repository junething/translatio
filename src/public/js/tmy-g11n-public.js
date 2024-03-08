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


        function tmy_hbs_fc() {
            //console.log("hubspot form loaded" );

            const allIframes = $('iframe');
            //console.log("iframes len: " + allIframes.length);

            allIframes.each(function(index, iframe) {
                const iframeContents = $(iframe).contents();

                const textNodes = iframeContents.find(':not(iframe, script, style)').contents().filter(function () {
                    return this.nodeType === 3 && this.nodeValue.trim() !== '';
                });
                textNodes.each(function (index, textNode) {

                    var textContent = textNode.nodeValue.trim();
                    textContent = textContent.replace(/^\n+|\n+$/g, '');
                    if (! sentencelist.includes(textContent)) {
                        sentencelist.push(textContent);
                    }
                    if (textContent in translationTable) {
                        textContent = translationTable[textContent];
                    }

                    const span = document.createElement('span');
                    span.className = 'tmyhighlighted';
                    $(span).attr('iframeid', $(iframe).attr('id'));
                    const highlightedText = document.createTextNode(textContent);
                    span.appendChild(highlightedText);
                    textNode.parentNode.replaceChild(span, textNode);
                });

                iframeContents.find('.tmyhighlighted').on('mouseover', handleMouseOver).
                                                       on('mouseout', handleMouseOut);
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



        function findAndUpdateTextNodesIframe(node, origText, newText) {
            $(node).find('iframe').each(function() {
                var iframe = $(this);
                iframe.contents().find('.tmyhighlighted').each(function() {
                    if ($(this).text().trim() === origText){
                        $(this).text(newText);
                    }
                });
            });
        }

        function findAndUpdateTextNodes(node, origText, newText) {
            if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '' &&
                  !$(node).closest('#wpadminbar').length &&
                  !$(node).closest('input, textarea').length &&
                  !$(node).closest('script').length &&
                  !$(node).closest('.popup-container').length &&
                  !$(node).closest('.translatio-logo-popup').length) {
                var textContent = node.nodeValue.trim();
                textContent = textContent.replace(/^\n+|\n+$/g, '');

                if (textContent === origText) {
                    textContent = newText;
                }
                const span = document.createElement('span');
                span.className = 'tmyhighlighted';
                const highlightedText = document.createTextNode(textContent);
                span.appendChild(highlightedText);
                node.parentNode.replaceChild(span, node);
            } else {
                for (const childNode of node.childNodes) {
                  findAndUpdateTextNodes(childNode, origText, newText);
                }
            }
        }

        function findAndPrintTextNodes(node, sentencelist) {
            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            //if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '') {
              if (node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== '' && 
                  !$(node).closest('#wpadminbar').length && 
                  !$(node).closest('input, textarea').length &&
                  !$(node).closest('script').length &&
                  !$(node).closest('.popup-container').length &&
                  !$(node).closest('.translatio-logo-popup').length) {

                //console.log(node.nodeValue);
                var textContent = node.nodeValue.trim();
                textContent = textContent.replace(/^\n+|\n+$/g, '');

                if (! sentencelist.includes(textContent)) {
                    sentencelist.push(textContent);
                }
                if (textContent in translationTable) {
                    textContent = translationTable[textContent];
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

    

        // Function to handle mouseover event
        function handleMouseOver() {

            const textNodePosition = $(this).offset();
            var textValue = $(this).text();
            var textValue = textValue.trim();
            textValue = textValue.replace(/^\n+|\n+$/g, '');

            // Create a hin box with "Click to edit" message
            const hint = $('<div class="hint"><button id="clickableButton" style="padding: 1;">' +
                         '<img id="iconImage" src="' +
                         tmy_g11n_ajax_obj.img_url + '/icons/t2.svg" alt="Translatio" style="width: 23px; height: 23px;">' +
                         'Edit Translation</button></div>');
 
            // Position the hint box relative to the mouse pointer
            const iframeId = $(this).attr('iframeid');

            if (typeof iframeId !== 'undefined' && iframeId !== false) {
                const iframe = $('#' + iframeId);
                    if (iframe.length > 0) {
                        const iframeOffset = iframe.offset();
                        textNodePosition.top += iframeOffset.top;
                        textNodePosition.left += iframeOffset.left;
                    } 
            } 
            hint.css({
              top: textNodePosition.top + $(this).height(), // Adjust the distance from the top
              left: textNodePosition.left
            });

            // Append the hint box to the body
            $('.hint').remove();
            $('body').append(hint);

            //$('#clickableButton').on('click', handleStartTranslation);

            $('#clickableButton').on('click', function() {
               handleStartTranslation(textValue);
            });

            // Change background to semi-transparent light grey on mouseover
            $(this).css('background-color', 'rgba(240, 240, 240, 0.8)'); // Semi-transparent light grey color
        }

        // Function to handle mouseout event
        function handleMouseOut() {
            // Remove background color on mouseout
            $(this).css('background-color', '');
       
            //$('.hint').remove();
        }

        function handleSendPageTranslation(referenceId) {

            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            var referenceId = $('meta[http-equiv="translatio-tmy-ref-id"]').attr('content');
            console.log("save page translation " + referenceId + " sentencelist leno=" + sentencelist.length);
            var table_saving = {};
            for (var i = 0; i < sentencelist.length; i++) {
                //console.log("input: -> " + i);
                var inputId = 'input_' + i;
                var transId = 'trans_' + i;
                var inputValue = $('#' + inputId).val();
                var transValue = $('#' + transId).val();
                if (transValue.trim() !== "") {
                    table_saving[inputValue] = transValue;
                    console.log("trans changed " + inputValue + "->" + transValue.trim());
                    findAndUpdateTextNodes(document.body, inputValue, transValue.trim());
                    findAndUpdateTextNodesIframe(document.body, inputValue, transValue.trim());
                } 
                //console.log("input:" + inputValue + " transValue: " + transValue + "translationTable: " + translationTable[inputValue]);
                /*
                if (translationTable[inputValue] !== transValue.trim()) {
                    //console.log("trans changed " + translationTable[inputValue] + "->" + transValue.trim());
                    //findAndUpdateTextNodes(document.body, translationTable[inputValue], transValue.trim());
                    //findAndUpdateTextNodesIframe(document.body, translationTable[inputValue], transValue.trim());
                    console.log("trans changed " + inputValue + "->" + transValue.trim());
                    findAndUpdateTextNodes(document.body, inputValue, transValue.trim());
                    findAndUpdateTextNodesIframe(document.body, inputValue, transValue.trim());
                }
                */
            }
            console.log("table_saving len =" + Object.keys(table_saving).length);
            //console.log('table_saving obj: ' + JSON.stringify(table_saving));

            var data = {
                'action': 'tmy_g11n_frontend_jquery_call',
                'operation': 'tmy_ops_save_translation',
                'language': contentLanguage,
                'referenceid': referenceId,
                'sentencelist': sentencelist,
                'obj': table_saving
            };

            /***
            $.ajax({
                url:     "http://localhost:3000/documents",
                method: 'POST',
                contentType: 'application/json',
                dataType: 'json',
                data: JSON.stringify(data),
                success: function(response) {
                    console.log('Response:', response);
                },
                error: function(xhr, status, error) {
                  console.log("Error message:", xhr.responseJSON);
                }
            });
            ***/

            $.ajax({
                type:    "POST",
                //async:   false,
                url:     tmy_g11n_ajax_obj.ajax_url,
                data:    data,
                success: function(response) {
                    //console.log(response);
                    var returnArr;
                    var returnMessage;
                    returnArr = JSON.parse(response);
                    returnMessage = returnArr["return_message"];
                    $('.table-status').text(returnMessage);
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                    $('.table-status').text("error");
                }
            });

        }

        function handleSaveTranslation(textValue) {

            $('.table-status').html('<div class="tmy_loader"></div>');

            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            var referenceId = $('meta[http-equiv="translatio-tmy-ref-id"]').attr('content');

            var origText = Object.keys(translationTable).find(key => translationTable[key] === textValue) ?? textValue;
            translationTable[origText] = $('.TmyInputClass').val();

            var transTableArray = {};
            Object.keys(translationTable).forEach(function(key) {
                transTableArray[key] = translationTable[key];
            });

            //console.log("objectArray stringify: " + JSON.stringify(transTableArray));

            findAndUpdateTextNodes(document.body, textValue, translationTable[origText]);
            findAndUpdateTextNodesIframe(document.body, textValue, translationTable[origText]);

            //console.log('Value of Orig: ', origText);
            //console.log('Save Translation: ', textValue);
            //console.log('Value of Input Field :', translationTable[origText]);

            //console.log('json obj: ' + JSON.stringify(translationTable));

            var data = {
                'action': 'tmy_g11n_frontend_jquery_call',
                'operation': 'tmy_ops_save_translation',
                'language': contentLanguage,
                'referenceid': referenceId,
                'sentencelist': sentencelist,
                'obj': transTableArray
            };
            $.ajax({
                type:    "POST",
                async:   false,
                url:     tmy_g11n_ajax_obj.ajax_url,
                data:    data,
                success: function(response) {
                    //console.log(response);
                    var returnArr;
                    var returnMessage;
                    returnArr = JSON.parse(response);
                    returnMessage = returnArr["return_message"];
                    $('.table-status').text(returnMessage);
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                    $('.table-status').text("error");
                }
            });
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
                //async:   false,
                url:     tmy_g11n_ajax_obj.ajax_url,
                data:    data,
                success: function(response) {
                   returnArr = JSON.parse(response);
                   translationTable = returnArr["return_data"];
                   returnMessage = returnArr["return_message"];
                   isDefaultLanguage = returnArr["return_is_default_lang"];
                   //console.log("get trans success, refid: " + referenceId + " table length: " + 
                   //             Object.keys(translationTable).length + " lang: " + contentLanguage + " isdefault: " +isDefaultLanguage);
                   findAndPrintTextNodes(document.body, sentencelist);
                   //console.log("sentencelist len: " + Object.keys(sentencelist).length);
                   $('.tmyhighlighted').on('mouseover', handleMouseOver).
                                        on('mouseout', handleMouseOut);

       
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                }
            });
        }



        function handleStartTranslation(textValue) {

            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            var referenceId = $('meta[http-equiv="translatio-tmy-ref-id"]').attr('content');
            //console.log("Start translation on: " + textValue + " " + referenceId);

            var defaultLanguageRemainder = "";
            if (isDefaultLanguage) {
                defaultLanguageRemainder = " (Default Language, Read Only)";
            } else {
                defaultLanguageRemainder = " (Type the translation in the editor box)";
            }

            var origText = Object.keys(translationTable).find(key => translationTable[key] === textValue) ?? textValue;
        
            var lang_code = contentLanguage.replace(/-/g, '_').toUpperCase();
            const popupBox = $(
              '<div class="popup-overlay" id="popupOverlay"></div>' +
              '<div class="popup-container">' +
                '<div class="popup-box">' +
                  '<p><b>' + origText + '</b></p>' +
                  '<p><img src="' + tmy_g11n_ajax_obj.img_url + '/flags/24/' + lang_code + '.png" title="' +  lang_code +'" alt="' + lang_code + "\" > " + lang_code +
                  '<p>' + defaultLanguageRemainder +
                  '<div id="tmy-translation-table"></div>' +
                  '<br>' +
                  '<div class="button-container">' +
                    '<button class="save-translation-button">Save&Publish </button><span style="font-size: smaller;">(Previous translations saved as revisions. Edits to global containers impact all pages)</span> ' +
             //       '<div class="table-status"><div class="tmy_loader"></div>checking status...</div>' +
                    '<button class="close-button">Cancel</button>' +
                  '</div>' +
                '</div>' +
              '</div>'
            );

            $('body').append(popupBox);

            const windowWidth = $(window).width();
            const windowHeight = $(window).height();
            const popupWidth = popupBox.width();
            const popupHeight = popupBox.height();
      
            const leftPosition = (windowWidth - popupWidth) / 2;
            const topPosition = (windowHeight - popupHeight) / 2;
          
            popupBox.css({
                left: leftPosition,
                top: topPosition
            });

            var table = $('<table id="tmy-translation-table">');

            var row = $('<tr>');
            var textareaField = $('<textarea>' + textValue + '</textarea>');
            textareaField.attr('rows', 5); // Set the number of rows as needed
            textareaField.attr('cols', 60); // Set the number of columns as needed
            textareaField.addClass('TmyInputClass');
            if (isDefaultLanguage) {
                textareaField.attr('readonly', true);
            }
            var textareaCell = $('<td>').append(textareaField);
            row.append(textareaCell);
            table.append(row);

            $('#popupOverlay').show();

            $('#tmy-translation-table').replaceWith(table);
            //$('.table-status').text(returnMessage);
            $('.table-status').text("Ref" + referenceId);

            $(document).on('click', function(e) {
                // Check if the clicked element is not inside the popup
                if (!$(e.target).closest('.hint').length) {
                    //$('#popupContent').hide();
                    $('.hint').remove();
                }
            });

            $('.save-translation-button').on('click', function() {
                $('.table-status').html('<div class="tmy_loader"></div>');
                handleSaveTranslation(textValue);
                $('#popupOverlay').hide();
                popupBox.remove();
            });

            $('#popupOverlay, .close-button').on('click', function() {
                $('#popupOverlay').hide();
                popupBox.remove();
            });

        }
        
        function handleStartPageTranslation(textValue) {

            var contentLanguage = $('meta[http-equiv="content-language"]').attr('content');
            var referenceId = $('meta[http-equiv="translatio-tmy-ref-id"]').attr('content');
            //console.log("Start Page translation on: " + textValue + " sentencelist len: " + Object.keys(sentencelist).length);
            //console.log("Start Page translation on: " + textValue + " translation table len: " + Object.keys(translationTable).length);

            var lang_code = contentLanguage.replace(/-/g, '_').toUpperCase();

            const popupBox = $(
              '<div class="popup-overlay" id="popupOverlay"></div>' +
                '<div class="popup-container">' +
                '<div class="popup-box">' +
                '<p><b>' + document.title + '</b></p>' +
                '<p><img src="' + tmy_g11n_ajax_obj.img_url + '/flags/24/' + lang_code + '.png" title="' +  lang_code +'" alt="' + lang_code + "\" > " + lang_code +
                '<p>Ref: ' + referenceId + '</p>' +
                '<p><div id="tmy-translation-table"></div></p>' +
                '<p><br></p>' +
                '<button class="send-page-translation-button">Save&Publish</button> <span style="font-size: smaller;">(Previous translations saved as revisions. Edits to global containers impact all pages)</span>' +
                //'<div class="table-status"><div class="tmy_loader"></div>checking status...</div>' +
                '<div class="table-status"></div>' +
                '<button class="close-button">Cancel</button>' +
                '</div>' +
                '</div>'
            );

            $('body').append(popupBox);
            $('#popupOverlay').show();

            // Center the popup in the middle of the screen
            const windowWidth = $(window).width();
            const windowHeight = $(window).height();
            const popupWidth = popupBox.width();
            const popupHeight = popupBox.height();
      
            const leftPosition = (windowWidth - popupWidth) / 2;
            const topPosition = (windowHeight - popupHeight) / 2;
          
            popupBox.css({
                left: leftPosition,
                top: topPosition
            });

            var table = $('<table id="tmy-translation-table"> <thead><tr><th></th><th>Current Translation</th><th>Type New Translation</th></tr></thead>');
            //$.each(sentencelist, function(index, sentence) {
            for (var i = 0; i < sentencelist.length; i++) {
                var row = $('<tr>');
                var languageCell = $('<td>').text(i);
                row.append(languageCell);

                //var inputField = $('<input type="text" value="' + sentencelist[i] + '">');
                var inputField = $('<textarea>' + sentencelist[i] + '</textarea>');
                inputField.attr('id', 'input_' + i); // Use a unique ID for each input
                inputField.attr('cols', 35);
                //inputField.attr('size', 40);
                inputField.addClass('TmyInputClass');
                //if (isDefaultLanguage) {
                    inputField.attr('readonly', true);
                //}

                var transField = $('<input type="text" value="' + 
                                    (translationTable?.[sentencelist[i]] ?? '')
                                    + '">');
                transField.attr('id', 'trans_' + i); // Use a unique ID for each input
                transField.attr('size', 40);
                transField.addClass('TmyInputClass');
                if (isDefaultLanguage) {
                    transField.attr('readonly', true);
                }

                var inputCell = $('<td>').append(inputField);
                var transCell = $('<td>').append(transField);
                row.append(inputCell);
                row.append(transCell); 
                table.append(row);
            }
            //});
            $('#tmy-translation-table').replaceWith(table);

            $('.send-page-translation-button').on('click', function() {
                $('.table-status').html('<div class="tmy_loader"></div>');
                handleSendPageTranslation(referenceId);
                popupBox.remove();
                $('#popupOverlay').hide();
            });

            // Close the popup on button click
            //$('.close-button').on('click', function() {
            $('#popupOverlay, .close-button').on('click', function() {
                popupBox.remove();
                $('#popupOverlay').hide();
            });
        }

        //console.log("Start Priv");

        var translationTable = {};
        var isDefaultLanguage;
        var sentencelist = [];
        var logo_popup = $('<div class="translatio-logo-popup">' +
                            '<div style="display: flex; align-items:center;">' +
                            '<img id="iconImage" src="' +
                            tmy_g11n_ajax_obj.img_url + '/icons/t1.svg" alt="Translatio" style="width: 145px;">' +
                            '</div><br>' +
                           '<button id="start-page-translation-button" class="start-page-translation-button">Edit Page Translation</button></div>');
        logo_popup.css('background-color', 'rgba(255, 255, 255, 0.8)'); // Adjust the values as needed
        $('body').append(logo_popup);
        logo_popup.fadeIn();

        handleBuildTranslationTable();
        //console.log("done translation table");
        //console.log("sentencelist len: " + sentencelist.length);


        $('#start-page-translation-button').on('click', function() {
                handleStartPageTranslation("PagePage");
        });

        $(document).on('click', function(e) {
            // Check if the clicked element is not inside the popup
            if (!$(e.target).closest('.hint').length) {
                //$('#popupContent').hide();
                $('.hint').remove();
            }
        });

    }); //jquery ready

})( jQuery );
