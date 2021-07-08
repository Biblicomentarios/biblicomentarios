(function($) {
    $.fn.ilj_keywords = function(options) {
        var elem = this;

        var settings = $.extend({
            inputField: '',
            errorMessage: '',
            requiresPro: false,
            sortable: true,
        }, options );

        var tipsoConfig = {
            width: '',
            maxWidth: '200',
            useTitle: true,
            delay: 100,
            speed: 500,
            background: '#32373c',
            color: '#eeeeee',
            size: 'small'
        };

        var keyword = {

            keywords: [],

            init: function(){
                var that = this;
                if(settings.sortable){
                    elem.find('ul.keyword-view').sortable({
                        opacity: 0.5,
                        helper: "clone",
                        forceHelperSize: true,
                        forcePlaceholderSize: true,
                        cursor: "move",
                        placeholder: "placeholder",

                            update: function(event, ui) {
                            that.reorderKeywords();
                        }
                    });
                }


                                                elem.find('ul.keyword-view').disableSelection();

                                elem.find('.tip').iljtipso(tipsoConfig);

                elem.on('keypress', 'input.keywordInput', function(e) {
                    if (e.keyCode === 13) {
                        elem.find('a.add-keyword').click();
                    }
                    return e.keyCode != 13;
                });

                elem.on('click', 'a.add-keyword', function(e) {
                	e.preventDefault();

                	 var keyword_input = $(this).siblings('input.keywordInput');

                	if (keyword_input.val().indexOf(',') !== -1) {
                		var keywords = keyword_input.val().split(',');
                		keywords.forEach(function(keyword, index) {
                        	keyword_value = that.sanitizeKeyword(keyword);
                            valid = that.validateKeyword(keyword_value);

                            if (!valid.is_valid) {
                                return;
                            }

                            that.addKeyword(keyword_value);
                    	});
                	} else {
                        keyword_value = that.sanitizeKeyword(keyword_input.val());
                        valid = that.validateKeyword(keyword_value);

                        if (!valid.is_valid) {
                            that.setError(valid.message);
                            return;
                        }

                        that.addKeyword(keyword_value);
                	}
                	keyword_input.val('');
                    that.clearError();
                	that.syncGui();
                	that.syncField();
                });

                this.initKeywords();
                this.syncGui();

                elem.on('click', '.keyword a.remove', function(e) {
                	e.preventDefault(); 
                	var index = $(this).parent('.keyword').data('id');
                	that.keywords.splice(index, 1);
                	that.syncGui();
                	that.syncField();
                });

                return this.keywords;
            },

            initKeywords: function() {
            	that = this;

                            	var input_data = $('<textarea/>').text(settings.inputField.val()).html(); 
                if (input_data != '' && input_data != null) {
                    var input_keywords = input_data.split(',');
                    input_keywords.forEach(function(keyword, index) {
                        that.addKeyword(keyword);
                    });
                }

            },

            addKeyword: function(keyword) {
                that = this;
                this.keywords.push(keyword);
            },

            sanitizeKeyword: function(keyword) {
                var keyword_sanitized = keyword
                            .replace(/\s*\{\s*/gu, " {")
                            .replace(/\s*\}\s*/gu, "} ")
                            .replace(/\s{2,}/gu, " ")
                            .replace(/^\s+|\s+$/gu, "")
                            .replace(/</g, "&lt;")
                            .replace(/>/g, "&gt;");
                return keyword_sanitized;
            },
             validateKeyword: function(keyword) {

                var status = {
                    is_valid: false,
                    message: "Unknown error",
                };
                var min_length = 2;
                var keyword_valid_check = keyword
                                            .replace(/\{.*?\}/gu, "")
                                            .replace(/\s/gu, "");

                for(var i = 0; i < this.keywords.length; i++) {
                    if (keyword.toLowerCase() == this.keywords[i].toLowerCase()) {
                        status.message = ilj_editor_translation.message_keyword_exists;
                        return status;
                    }
                }

                if (keyword_valid_check === "") {
                    status.message = ilj_editor_translation.message_no_keyword;
                    return status;
                }

                if (keyword_valid_check.length < min_length) {
                    status.message = ilj_editor_translation.message_length_not_valid;
                    return status;
                } 

                if (/(\s?\{[+-]*\d+\}\s?){2,}/.test(keyword)) {
                    status.message = ilj_editor_translation.message_multiple_placeholder;
                    return status;
                }

                var keywords_count = this.keywords.length;
                if(settings.requiresPro == true && ilj_editor_basic_restriction.is_active == true){
                    if(keywords_count >= ilj_editor_basic_restriction.blacklist_limit ){
                        status.message = '<p>' + ilj_editor_translation.message_limited_blacklist_keyword + '</p>';
                        status.message += '<p>' + ilj_editor_translation.message_limited_blacklist_keyword_upgrade + '.</p>';
                        return status;
                    }
                }

                status.is_valid = true;
                status.message = "";

                return status;
             },

            syncGui: function() {
            	var that = this;
                elem.find('ul.keyword-view li').remove();
                if (this.keywords.length > 0) {
                    this.keywords.forEach(function (keyword, index) {
                        elem.find('ul.keyword-view').append($(that.renderKeyword(keyword, index)));
                    });
                    elem.find('.tip').iljtipso(tipsoConfig);
                } else {
                    elem.find('ul.keyword-view').append($('<li>' + ilj_editor_translation.no_keywords + '</li>'));
                }


                          },

            syncField: function() {
            	settings.inputField.val(this.keywords.join(','));
            },

            renderKeyword: function(keyword, index) {
                keyword_print = keyword
                                    .replace(/\{(\d+)\}/g, '<span class="exact tip" title="' + ilj_editor_translation.gap_hover_exact + ' $1">$1</span>')
                                    .replace(/\{\-(\d+)\}/g, '<span class="max tip" title="' + ilj_editor_translation.gap_hover_max + ' $1">$1</span>')
                                    .replace(/\{\+(\d+)\}/g, '<span class="min tip" title="' + ilj_editor_translation.gap_hover_min + ' $1">$1</span>');
            	return '<li class="keyword" data-id="'+index+'"><a class="dashicons dashicons-dismiss remove"></a>'+keyword_print+'</li>';
            },

            reorderKeywords: function() {
                order = [];

                elem.find('li').each(function() {
                   var id = $(this).data('id');

                   if (id === undefined) {
                       return;
                   }

                   order.push(id);
                });

                new_keywords = [];

                $.each(order, function(key, position) {
                    new_keywords.push(that.keywords[position]);
                });

                that.keywords = new_keywords;
                that.syncGui();
                that.syncField();

                return true;
            },

            setError: function(message) {
                settings.errorMessage.html(message);
                settings.errorMessage.show();
            },

            clearError: function() {
                settings.errorMessage.html('');
                settings.errorMessage.hide();
            },

                    };

        keyword.init();
    }

}(jQuery));