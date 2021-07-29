(function (OC, window, $, undefined) {
    OC.describo = OC.describo || {};

    var createDescribo = {
        attach: function (menu) {
            menu.addMenuEntry({
                id: "createDescribo",
                displayName: "Show metadata",
                iconClass: "icon-rds-research-small",
                fileType: "folder",
                templateName: "ro-crate-metadata.json",
                actionHandler: function (fileName) {
                    const urlParams = new URLSearchParams(window.location.search);
                    const dir = urlParams.get('dir');
                    location = OC.generateUrl("apps/describo") + "/?folderpath=" + dir
                },
            });
        },
    };

    const folderAction = {
        init: function (mimetype, fileActions) {
            var self = this;
            fileActions.registerAction({
                name: "folderAction",
                displayName: "Show metadata",
                mime: mimetype,
                permissions: OC.PERMISSION_UPDATE,
                type: OCA.Files.FileActions.TYPE_DROPDOWN,
                iconClass: "icon-rds-research-small",
                actionHandler: function (filename, context) {
                    var fileName = "";
                    var mimetype = context.$file.data("mime");
                    var dir = context.fileList.getCurrentDirectory();

                    if (!dir.endsWith("/")) {
                        dir += "/";
                    }

                    fileName = dir + filename;
                    if (mimetype === "httpd/unix-directory") {
                        fileName += "/"
                    }

                    location = OC.generateUrl("apps/describo") + "/?folderpath=" + fileName
                },
            });
        },
    };

    function attachFilelist(fileList) {
        // add file actions in here using
        var mimes = ["httpd/unix-directory"];
        mimes.forEach((item) => {
            folderAction.init(item, fileList.fileActions);
        });
    }

    OC.describo.FilePlugin = {
        attach: attachFilelist
    }

    OC.Plugins.register("OCA.Files.NewFileMenu", createDescribo);
    OC.Plugins.register('OCA.Files.FileList', OC.describo.FilePlugin);
})(OC, window, jQuery);