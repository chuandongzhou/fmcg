function onCheckChange(parent_selectors, target_selectors) {
    var parents = $(parent_selectors);
    var targets = $(target_selectors);
    parents.change(function () {
        targets.prop('checked', $(this).prop('checked'));
    });

    targets.change(function () {
        parents.prop('checked', targets.length === targets.filter(':checked').length);
    });
}
