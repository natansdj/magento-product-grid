var amPattribute = new Class.create();

amPattribute.prototype = {

    attributeColumnId: 1,
    preventSimilarOption: true,
    choosenAttrIndex: JSON.parse("{}"),

    initialize: function (title) {
        this.title = title;
    },

    initializeTrGrid: function (attributeColumns) {
        var template = this.getAttributeColumnTemplate(),
            obj = this,
            attrSelect = $('attributes-select');

        //Initialize each choosen attribute
        attributeColumns.each(function (attr) {
            attr.attribute_list = attrSelect.innerHTML;
            attr.id = obj.attributeColumnId++;
            attr.is_disabled = attr.allow_to_edit !== 1 ? 'disabled="disabled"' : '';

            var html = template.evaluate(attr);
            $('attribute-columns-table').down('tbody').insert(html);

            var curAttrSel = $('impgrid-attribute-select_' + attr.id);
            if (curAttrSel.down('select option[value=' + attr.attribute_id + ']')) {
                curAttrSel.down('select option[value=' + attr.attribute_id + ']')
                    .setAttribute('selected', 'selected');
            }
            obj.initCombobox(curAttrSel.down('.chosen-select'));
            obj.choosenAttrIndex[attr.id] = curAttrSel.down('.chosen-select').options.selectedIndex;
        });

        //disable choosen attribute on initialization
        obj.chosenRefresh();

        $$('.im-delete-attribute').each(function (elem) {
            Event.observe(elem, 'click', function (event) {
                obj.deleteAttributeColumn(event);
            });
        });

        //observe choosen attribute on changed
        document.body.on('change', '.chosen-select', obj.chosenEvent.bind(this));

        this.observeLastAttributeSelect();
        this.addAttributeColumn();
    },

    resetSelectElement: function (selectElement) {
        var options = selectElement.options;

        // Look for a default selected option
        for (var i = 0, iLen = options.length; i < iLen; i++) {
            options[i].disabled = false;
        }
    },

    chosenRefresh: function () {
        var obj = this;

        if (!obj.preventSimilarOption) return;

        $$('.chosen-select').each(function (element) {
            if (!element.options) return;
            var options = element.options,
                updated = false;

            //reset
            obj.resetSelectElement(element);

            //assign
            if (obj.choosenAttrIndex) {
                for (var k in obj.choosenAttrIndex) {
                    if (obj.choosenAttrIndex.hasOwnProperty(k)) {
                        var chosenIndex = obj.choosenAttrIndex[k];
                        if (chosenIndex !== 0) {
                            options[chosenIndex].disabled = true;
                            updated = true;
                        }
                    }
                }
            }

            if (updated) {
                element.fire('chosen:updated');
            }
        });
    },

    chosenEvent: function (event) {
        var obj = this,
            element = Event.element(event);

        if (!element.options) return;

        var newIndex = element.options.selectedIndex,
            input = element.previous('.attribute_id');

        if (!input) return;
        var chosenId = input.readAttribute('data-id');

        if (newIndex === 0) {
            delete obj.choosenAttrIndex[chosenId];
        } else if (newIndex) {
            obj.choosenAttrIndex[chosenId] = newIndex;
        }

        obj.chosenRefresh();
    },

    initCombobox: function (element) {
        new Chosen(element, {
            width: "250px",
            search_contains: true,
            allow_single_deselect: true
        });
    },

    observeLastAttributeSelect: function () {
        var obj = this;
        Event.observe($('attribute-columns-table'), 'change', function (event) {

            if (Event.element(event) === event.findElement('select.impgrid-attribute-select')) {
                Event.element(event).previous('.attribute_id').value = Event.element(event).value;
            }
            if (Event.element(event)
                === $('attribute-columns-table').down('tbody').childElements().last().down('select.impgrid-attribute-select')) {
                obj.addAttributeColumn();
            }
        });
    },

    addAttributeColumn: function (event) {
        var template = this.getAttributeColumnTemplate(),
            obj = this,
            attrColTable = $('attribute-columns-table'),
            tbody = attrColTable.down('tbody');

        tbody.insert(template.evaluate({
            'id': obj.attributeColumnId++,
            'attribute_list': $('attributes-select').innerHTML
        }));
        if (attrColTable.down('tbody').childElements().last().previous()) {
            attrColTable.down('tbody').childElements().last().previous().down('.im-delete-attribute').show();
        }
        attrColTable.down('tbody').childElements().last().down('.im-delete-attribute').hide();

        this.initCombobox(attrColTable.down('tbody').childElements().last().down('.chosen-select'));
        Event.observe(attrColTable.down('tbody').childElements().last().down('.im-delete-attribute')
            , 'click', function (event) {
                obj.deleteAttributeColumn(event);
            });
    },

    deleteAttributeColumn: function (event) {
        var obj = this,
            element = Event.element(event).up('tr').down('input.attribute_id'),
            chosenId = element.readAttribute('data-id');

        if (typeof element !== 'undefined' && typeof chosenId !== 'undefined') {
            delete obj.choosenAttrIndex[chosenId];
            obj.chosenRefresh();
        }

        Event.element(event).up('tr').remove();
    },

    getAttributeColumnTemplate: function () {
        return new Template(
            '<tr class="even" >\
              <td id="impgrid-attribute-select_#{id}" data-id="#{id}" >\
                  <input class="attribute_id" type="hidden" name="pattribute[#{id}][attribute_id]" value="#{attribute_id}" data-id="#{id}" />\
                  #{attribute_list}\
              </td>\
              <td><input type="text" class="custom-title" name="pattribute[#{id}][custom_title]" value="#{custom_title}" /></td>\
              <td><span class="im-delete-attribute"></span></td>\
            </tr>'
        );
    },

    showConfig: function (url) {
        Window.keepMultiModalWindow = true;
        attributeDialog = new Window({
            draggable: true,
            closable: true,
            className: "magento",
            windowClassName: "popup-window",
            title: this.title,
            resizable: true,
            width: 720,
            height: 600,
            zIndex: 1000,
            hideEffect: Element.hide,
            showEffect: Element.show,
            showProgress: true,
            minimizable: false,
            maximizable: false,
            destroyOnClose: true,
            recenterAuto: false,
            id: 'attributeDialog'
        });
        attributeDialog.setAjaxContent(
            url,
            {method: 'get'}, true, true
        );

    },

    CheckAll: function () {
        $$("table#standard-columns-table input[type='checkbox']:unchecked").each(function (obj) {
            obj.checked = true;
        });
    },

    unCheckAll: function () {
        $$("table#standard-columns-table input[type='checkbox']:checked").each(function (obj) {
            obj.checked = false;
        });
    },

    closeConfig: function () {
        attributeDialog.close();
    },

    disableSave: function () {
        var saveGroup = $('save-group');
        if (saveGroup) {
            saveGroup.setAttribute('disabled', 'disabled');
            saveGroup.addClassName('disabled');
        }
    }
};

pAttribute = new amPattribute('Grid Columns');
