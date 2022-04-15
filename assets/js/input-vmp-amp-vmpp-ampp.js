(function($) {
  function initialize_field($el) {
    //$el.doStuff();
  }

  if (acf) {
    var VMPAMPVMPPAMPP = acf.Field.extend({
      type: 'vmp-amp-vmpp-ampp',
      select2: false,

      wait: 'load',

      events: {
        removeField: 'onRemove',
      },

      $input: function() {
        return this.$('select');
      },

      initialize: function() {
        // vars
        var $select = this.$input();

        // inherit data
        this.inherit($select);

        // select2
        if (this.get('ui')) {
          // populate ajax_data (allowing custom attribute to already exist)
          var ajaxAction = this.get('ajax_action');
          if (!ajaxAction) {
            ajaxAction = 'acf/fields/' + this.get('type') + '/query';
          }

          // select2
          this.select2 = acf.newSelect2($select, {
            field: this,
            ajax: this.get('ajax'),
            multiple: this.get('multiple'),
            placeholder: this.get('placeholder'),
            allowNull: this.get('allow_null'),
            ajaxAction: ajaxAction,
          });
        }
      },

      onRemove: function() {
        if (this.select2) {
          this.select2.destroy();
        }
      },
    });

    acf.registerFieldType(VMPAMPVMPPAMPP);
  }

  if (typeof acf.add_action !== 'undefined') {
    // acf.fields.vmp = acf.fields.select.extend({

    // 	type: 'vmp',
    // 	minimumInputLength: 1,
    // 		quietMillis: 100 //or 100 or 10

    // });

    /*
     *  ready append (ACF5)
     *
     *  These are 2 events which are fired during the page load
     *  ready = on page load similar to $(document).ready()
     *  append = on new DOM elements appended via repeater field
     *
     *  @type	event
     *  @date	20/07/13
     *
     *  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
     *  @return	n/a
     */

    acf.add_action('ready append', function($el) {
      // search $el for fields of type 'FIELD_NAME'
      acf.get_fields({ type: 'vmp-amp-vmpp-ampp' }, $el).each(function() {
        initialize_field($(this));
      });
    });

    acf.add_filter('select2_args', function(args, $select, settings) {
      var parents = $select.parents('.acf-field-vmp-amp-vmpp-ampp');
      if (parents.length) {
        var original = null;
        if (typeof args.ajax.data === 'function') {
          original = args.ajax.data;
        }
        args.ajax.data = function(params) {
          var originalParams =
            original && typeof original === 'function'
              ? original(params)
              : params;

          var vtmOrVmp = $('[data-type="vtm-vmp"] select');
          var vtm = $('[data-type="vtm"] select');
          var vmp = $('[data-type="vmp"] select');

          if (vtmOrVmp.length) {
            var option = $('option:selected', vtmOrVmp).text();
            if (option) {
              var parentType = option.includes('VTM') ? 'vtm' : 'vmp';
              originalParams.parent_type = parentType;
              originalParams.parent_id = vtmOrVmp.val();
            }
          }

          if (
            !originalParams.parent_type &&
            !originalParams.parent_id &&
            vtm.length
          ) {
            originalParams.parent_type = 'vtm';
            originalParams.parent_id = vtm.val();
          }

          if (
            !originalParams.parent_type &&
            !originalParams.parent_id &&
            vmp.length
          ) {
            originalParams.parent_type = 'vmp';
            originalParams.parent_id = vmp.val();
          }
          return originalParams;
        };
      }

      return args;
    });
  } else {
    /*
     *  acf/setup_fields (ACF4)
     *
     *  This event is triggered when ACF adds any new elements to the DOM.
     *
     *  @type	function
     *  @since	1.0.0
     *  @date	01/01/12
     *
     *  @param	event		e: an event object. This can be ignored
     *  @param	Element		postbox: An element which contains the new HTML
     *
     *  @return	n/a
     */

    $(document).on('acf/setup_fields', function(e, postbox) {
      $(postbox)
        .find('.field[data-field_type="vmp-amp-vmpp-ampp"]')
        .each(function() {
          initialize_field($(this));
        });
    });
  }
})(jQuery);
