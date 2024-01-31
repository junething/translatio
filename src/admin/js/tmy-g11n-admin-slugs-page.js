(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

        console.log("STARTING ...");
        //var mainRulesTable = [
            //{ lang: 'zh-cn', slug: '关于', url: 'about' },
            //{ lang: 'm2', slug: 'm22', url: 'm23' },
            //{ lang: 'm3', slug: 'm32', url: 'm33' }
        //];
        var mainRulesTable = [];

        var detectedValues = [
            { lang: 'zh-cn', slug: '关于我们', url: 'about' },
            { lang: 'e', slug: 'f', url: 'd' }
        ];

        var data = {
            'action': 'tmy_g11n_admin_slugs_ops',
            'operation': 'tmy_g11n_admin_slugs_ops_getrules'
        };

        $.ajax({
            type:    "POST",
            async:   false,
            url:     ajaxurl,
            data:    data,
            success: function(response) {
                console.log(response);
                var returnArr;
                var mainRulesTable;
                var extraValues;
                var returnMessage;
                returnArr = JSON.parse(response);
                returnMessage = returnArr["return_message"];
                mainRulesTable = returnArr["main_rules"];
                if (mainRulesTable.length > 0) {
                    var tableHTML = `Following are detected from the system:<br>`;
                    tableHTML += `<table id="detectTable">
                                        <thead>
                                            <tr>
                                                <th id="langField">Language</th>
                                                <th id="typeField">Type</th>
                                                <th id="urlField" style="width: 350px;">URL</th>
                                                <th id="slugField" style="width: 350px;">Slug</th>
                                                <th class="actionColumn"></th>
                                            </tr>
                                        </thead>
                                        <tbody>`;
                    mainRulesTable.forEach(function (value, index) {
                        tableHTML += `<tr>
                        <td>${value.lang}</td>
                        <td>${value.type}</td>
                        <td>${value.url}</td>
                        <td>${value.slug}</td>
                        <td>
                            <button class="appendBtn"
                                    data-lang="${value.lang}"
                                    data-url="${value.url}"
                                    data-type="${value.type}"
                                    data-slug="${value.slug}">Append</button>
                        </td>
                        </tr>`;
                    });
                    tableHTML += `</tbody> </table>`;
                    $('#detectedValuesContent').html(tableHTML);
                }

                extraValues = returnArr["main_rules_extra"];
                if (extraValues.length > 0) {
                    extraValues.forEach(function (values) {
                      addRowNew(true, values.lang, values.url, values.slug, values.type);
                    });
                }

            },
            error:   function(jqXHR, textStatus, errorThrown ) {
                returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                $('.table-status').text("error");
            }
        });

        $('#detectedValuesContent').on('click', '.appendBtn', function () {
            var lang = $(this).data('lang');
            var url = $(this).data('url');
            var slug = $(this).data('slug');
            var type = $(this).data('type');
            addRowNew(true, lang, url, slug, type);
        });

        function addRowNew(contentEditable, langValue, urlValue, slugValue, typeValue) {
            var newRow = '<tr' + (contentEditable ? ' class="EditableRow"' : ' class="nonEditableRow"') + '>' +
                '<td contenteditable="' + contentEditable + '">' + langValue + '</td>' +
                '<td contenteditable="' + contentEditable + '">' + typeValue + '</td>' +
                '<td contenteditable="' + contentEditable + '">' + urlValue + '</td>' +
                '<td contenteditable="' + contentEditable + '">' + slugValue + '</td>' +
                '<td class="actionColumn">' + (contentEditable ? '<button class="button button-primary removeBtn">Remove</button>' : '') + '</td>' +
                '</tr>';

            $('#editableTable tbody').append(newRow);
        }

        function removeRow() {
            $(this).closest('tr').remove();
        }

        function saveTable() {
            // Add your save logic here
           var dataArray = [];

            // Loop through each editable row
            $('#editableTable tbody tr:not(.nonEditableRow)').each(function(index, row) {
                var rowData = [];

                // Loop through each editable cell in the row
                $(row).find('td[contenteditable="true"]').each(function() {
                    rowData.push($(this).text());
                });

                // Add the row data to the array
                dataArray.push(rowData);
            });

            var data = {
                'action': 'tmy_g11n_admin_slugs_ops',
                'operation': 'tmy_g11n_admin_slugs_ops_saverules',
                'data': dataArray
            };

            var returnMessage;

            $.ajax({
                type:    "POST",
                async:   false,
                url:     ajaxurl,
                data:    data,
                success: function(response) {
                    console.log(response);
                    var returnArr;
                    returnArr = JSON.parse(response);
                    returnMessage = returnArr["return_message"];
    
                },
                error:   function(jqXHR, textStatus, errorThrown ) {
                    returnMessage = "Error, status = " + jqXHR.status + ", " + "textStatus: " + textStatus + "ErrorThrown: " + errorThrown;
                    $('.table-status').text("error");
                }
            });

            console.log(dataArray);
            alert(returnMessage);
        }

        $('#addRow').on('click', function() {
            addRowNew(true, "", "", "");
        });

        $('#saveTable').on('click', function() {
            saveTable();
        });

        $('#editableTable').on('click', '.removeBtn', removeRow);

   }); //ready

})( jQuery );
