<div>
    <div class="ipt-wrap">
        <div class="ipt-first">
            <span class="input">
                <button type="button" id="<?= $this->id; ?>-book-button" data-action='[
                    {
                        "key": 1, "listener": "click", "action": [
                            {"key": 1, "type": "dom.popup", "selector": "#group-selector-tpl", "aniIn": "fadeIn", "id": "<?= $this->id; ?>"},
                            {"key": 2, "type": "message.request", "uri": "<?= \phpOMS\Uri\UriFactory::build('{/base}/admin/group?filter=some&limit=10'); ?>", "method": "GET", "request_type": "json"},
                            {"key": 3, "type": "dom.table.append", "id": "acc-table", "aniIn": "fadeIn", "data": [], "bindings": {"id": "id", "name": "name/0"}, "position": -1},
                            {"key": 4, "type": "message.request", "uri": "<?= \phpOMS\Uri\UriFactory::build('{/base}/admin/group?filter=some&limit=10'); ?>", "method": "GET", "request_type": "json"},
                            {"key": 5, "type": "dom.table.append", "id": "grp-table", "aniIn": "fadeIn", "data": [], "bindings": {"id": "id", "name": "name/0"}, "position": -1}
                        ]
                    }
                ]'><i class="g-icon">book</i></button>
                <input type="text" list="<?= $this->id; ?>-datalist" id="<?= $this->id; ?>" name="receiver" data-action='[
                    {
                        "key": 1, "listener": "keyup", "action": [
                            {"key": 1, "type": "validate.keypress", "pressed": "!13!37!38!39!40"},
                            {"key": 2, "type": "utils.timer", "id": "<?= $this->id; ?>", "delay": 500, "resets": true},
                            {"key": 3, "type": "dom.datalist.clear", "id": "<?= $this->id; ?>-datalist"},
                            {"key": 4, "type": "message.request", "uri": "{/base}/{/lang}/api/admin/find/group?search={!#<?= $this->id; ?>}", "method": "GET", "request_type": "json"},
                            {"key": 5, "type": "dom.datalist.append", "id": "<?= $this->id; ?>-datalist", "value": "id", "text": "name"}
                        ]
                    },
                    {
                        "key": 2, "listener": "keydown", "action" : [
                            {"key": 1, "type": "validate.keypress", "pressed": "13|9"},
                            {"key": 2, "type": "message.request", "uri": "{/base}/{/lang}/api/admin/find/group?search={!#<?= $this->id; ?>}", "method": "GET", "request_type": "json"},
                            {"key": 3, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>-idlist", "value": "{0/id}", "data": ""},
                            {"key": 4, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>-taglist", "value": "<span id=\"<?= $this->id; ?>-taglist-{0/id}\" class=\"tag red\" data-id=\"{0/id}\"><i class=\"g-icon\">close</i> {0/name}</span>", "data": ""},
                            {"key": 5, "type": "dom.setvalue", "overwrite": true, "selector": "#<?= $this->id; ?>", "value": "", "data": ""}
                        ]
                    }
                ]'>
                <datalist id="<?= $this->id; ?>-datalist"></datalist>
                <input name="datalist-list" type="hidden" id="<?= $this->id; ?>-idlist"<?= $this->isRequired() ? ' required' : ''; ?>>
            </span>
        </div>
        <div class="ipt-second"><button><?= $this->getHtml('Add', '0', '0'); ?></button></div>
    </div>
    <div class="box taglist" id="<?= $this->id; ?>-taglist" data-action='[
        {
            "key": 1, "listener": "click", "selector": "#<?= $this->id; ?>-taglist span fa", "action": [
                {"key": 1, "type": "dom.getvalue", "base": "self"},
                {"key": 2, "type": "dom.removevalue", "selector": "#<?= $this->id; ?>-idlist", "data": ""},
                {"key": 3, "type": "dom.remove", "base": "self"}
            ]
        }
    ]'></div>
</div>