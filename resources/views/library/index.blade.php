<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/themes/default/style.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    
    .card-9 {
        height: 535px;
        margin-left: -2.4rem !important;
    }

    .card-3 {
        border-width: 1px;
        height: 535px;
    }

    .row-2 {
        white-space: nowrap;
        padding-right: 1.9rem !important;
    }

    .image-col:hover {
        transform: scale(1.1);
        border: 0px solid #007bff;
    }

    .image-col {
        transition: transform 0.3s ease, background-color 0.3s ease, border 0.3s ease;
    }

</style>
</head>
<body>
    @include('layouts.sidebar')

    <div class="container mt-2">
        <div class="card mt-4">
            <div class="card-body">
            <h1>Folders</h1>
                <div class="row">
                    <div class="col-md-3 col-4 mt-2 row-2">
                        <div class="card card-3">
                            <div class="container mt-4" id="tree-container">
                                <ul>
                                    <li data-id="1">Library
                                        <ul>
                                        
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div id="data"></div>
                            <div id="code"></div>
                        </div>
                    </div>
                    <div class="col-md-9 col-4 mt-2">
                        <div class="card card-9">
                            <div class="container mt-2">
                                <span class="text-muted fw-light" id="libraryPath">Library /</span>
                                <span id="currentTab">Images</span>
                                <span id="currentFolderName"></span>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <div class="img-thumbs img-thumbs-hidden" id="img-preview"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.11/jstree.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jstree-wholerow/dist/jstree.wholerow.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jstree-contextmenu/dist/jstree.contextmenu.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jstree-dnd/dist/jstree.dnd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    $(document).ready(function () {
        var newTree = [];
        var jData;

        function getChildren(jObj, children) {
            $.each(children.children, function (i, obj) {
                var childObj = jObj[obj];
                if (childObj['li_attr'].hasOwnProperty('data-id')) {
                    var thisIndex = jObj[obj].data.id - 1;
                    var left = childObj.children.length ? thisIndex * 2 : (thisIndex * 2) - 1;
                    var right = childObj.children.length ? ((childObj.children.length * 2) + left + 1) : left + 1;
                    newTree.push({ id: childObj.li_attr["data-id"], name: childObj.text, left: left, right: right });
                }
                getChildren(jData, jObj[obj]);
            });
        }

        function fetchFolders() {
            axios.get('{{ route("getFolders.library") }}')
                .then(function (response) {
                    var folders = response.data;
                    folders.forEach(function (folder) {
                        var parentNode = folder.parentId ? $('#tree-container').jstree().get_node(folder.parentId) : '#';
                        var newNode = { id: folder.id, parent: parentNode, text: folder.folderName, icon: "jstree-folder" };
                        $('#tree-container').jstree().create_node(parentNode, newNode, 'last');
                    });
                })
                .catch(function (error) {
                    console.error('Error fetching folders:', error);
                });
        }

        $('#tree-container').jstree({
            "core": {
                "check_callback": true,
            },
            "plugins": ["contextmenu", "dnd", "state", "wholerow"],
            "contextmenu": {
                "items": function (node) {
                    var menuItems = {
                        "uploadImage": {
                            "label": "Upload Image",
                            "action": function () {
                                var folderId = $('#tree-container').jstree().get_selected();
                                
                                if (folderId.length > 0) {
                                    $("#imageInput").data("folderId", folderId[0]);
                                    console.log('id::', folderId);
                                    $("#imageInput").click();
                                } else {
                                    alert("Please select a folder before uploading an image.");
                                }
                            }
                        },
                        "create": {
                            "label": "Create",
                            "action": function () {
                                var nodeName = prompt("Enter the folder name:");
                                if (nodeName) {
                                    var parentNodeId = node.id || '#';
                                    var newNode = { text: nodeName, icon: "jstree-folder" };
                                    $('#tree-container').jstree().create_node(node, newNode, 'last', function (newNode) {
                                        createFolderOnServer(newNode, parentNodeId);
                                    });
                                }
                            }
                        },
                        "edit": {
                            "label": "Rename",
                            "action": function () {
                                var nodeName = prompt("Enter the new folder name:", node.text);
                                if (nodeName) {
                                    $('#tree-container').jstree().rename_node(node, nodeName);
                                    editFolderOnServer(node.id, nodeName);
                                }
                            }
                        },
                        "remove": {
                            "label": "Delete",
                            "action": function () {
                                if (node.id) {
                                    $('#tree-container').jstree().delete_node(node);
                                    console.log('Deleting node with ID:', node.id);
                                    deleteFolderOnServer(node.id);
                                }
                            }
                        },
                        "ccp": false
                    };

                    if (node.id === 'j1') {
                        delete menuItems.remove;
                    }

                    return menuItems;
                }
            }
        }).on("select_node.jstree", function (e, data) {
        // Handle folder selection
        var selectedFolderId = data.node.id;
            console.log('selectedFolderId', selectedFolderId);
        // Call a function to fetch and display images based on the selected folder
        fetchAndDisplayImages(selectedFolderId);
        }).on("move_node.jstree", function (e, data) {
            console.log(data);
            newTree = [];
            jData = data.new_instance._model.data;
            console.log(jData);
            $.each(jData, function (i, obj) {
                if ($(this)[0].id === 'j1_1') {
                    var currentObj = $(this)[0];
                    if (currentObj['li_attr'].hasOwnProperty('data-id')) {
                        var right = data.instance['_cnt'] * 2;
                        newTree.push({ id: currentObj.li_attr["data-id"], name: currentObj.text, left: 1, right: right });
                    }
                    getChildren(jData, currentObj);
                }
            });
            $('#code').jJsonViewer(JSON.stringify(newTree));
        });

        function fetchAndDisplayImages(folderId) {
            console.log('Fetching images for folder ID:', folderId);
            
            axios.get('{{ route("getImages.library") }}', {
                params: {
                    parentId: folderId,
                }
            })
            .then(function (response) {
                console.log('Images received:', response.data);
                
                // Clear existing images
                $('#img-preview').empty();

                // Display each image in the img-thumbs container
                for (var i = 0; i < response.data.length; i += 6) {
                    var row = $('<div class="row mt-4" style="padding-left: 2rem;"></div>');

                    for (var j = i; j < i + 6 && j < response.data.length; j++) {
                        var image = response.data[j];
                        var imageUrl = '{{ asset("storage/images/") }}/' + image.fileName;
                        
                        console.log('Processing image:', image.fileName, 'URL:', imageUrl);

                        var imgTag = '<div class="col-md-2 image-col " style="position: relative; margin-right: -8px;">' +
                        '<img style="box-shadow: rgb(209, 202, 202) 0px 0px 5px 2px;" src="' + imageUrl + '" alt="' + image.fileName + '" class="img-thumbnail download-image" data-image-url="' + imageUrl + '" data-image-name="' + image.fileName + '" onerror="console.error(\'Failed to load image:\', \'' + imageUrl + '\')" onload="console.log(\'Image loaded successfully:\', \'' + image.fileName + '\')">' +
                        '<button class="btn btn-danger btn-sm remove-btn" style="position: absolute; display: flex; justify-content: center; align-items: center; font-size: 0.6rem; top: -5px; right: 6px; width: 18px; height: 18px; border-radius: 10px; font-weight: bold; cursor: pointer;" data-image-id="' + image.id + '">X</button>' +
                        '<p class="text-center mt-2">' + image.fileName + '</p>' +
                        '</div>';

                        row.append(imgTag);
                    }

                    $('#img-preview').append(row);
                }

                console.log('Total images displayed:', response.data.length);

                $('.image-col').hover(
                    function () {
                        $(this).addClass('img-hover');
                    },
                    function () {
                        $(this).removeClass('img-hover');
                    }
                );

                $('.remove-btn').on('click', function() {
                    var imageId = $(this).data('image-id');
                    console.log('imageId', imageId);
                    handleRemoveImage(imageId);
                });

                $('.download-image').on('click', function() {
                    var imageUrl = $(this).data('image-url');
                    var imageName = $(this).data('image-name');
                    downloadImage(imageUrl, imageName);
                });

            })
            .catch(function (error) {
                console.error('Error fetching images:', error);
            });
        }

        function downloadImage(imageUrl, imageName) {
            var downloadLink = document.createElement('a');
            downloadLink.href = imageUrl;
            downloadLink.download = imageName;

            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        function handleRemoveImage(imageId) {
            axios.get('{{ route("deleteImage.library") }}' , { 
                params: {
                    imageId: imageId,
                    }
                })
                .then(function (response) {
                    console.log(response.data.message);
                    removeImageColumn(imageId);
                })
                .catch(function (error) {
                    console.error('Error deleting image:', error);
                });
        }

        function removeImageColumn(imageId) {
            $('.remove-btn[data-image-id="' + imageId + '"]').closest('.col-md-2').remove();
        }

        $('#tree-container').append('<input type="file" id="imageInput" accept="image/jpeg,image/jpg,image/png,image/gif,image/bmp,image/webp" style="display: none;">');

        $('#imageInput').on('change', function () {
            var file = this.files[0];
            var parentId = $("#imageInput").data("folderId");
            console.log('File selected:', file);
            console.log('File type:', file.type);
            console.log('File extension:', file.name.split('.').pop());
            console.log('Parent ID:', parentId);
            
            if (file && parentId) {
                var formData = new FormData();
                formData.append('image', file);
                formData.append('parentId', parentId);

                axios.post('{{ route("uploadImage.library") }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    },
                })
                .then(function (response) {
                    console.log('Image uploaded successfully:', response.data.filename);

                    var uploadedImageFilename = response.data.filename;
                    fetchAndDisplayImages(parentId);
                })
                .catch(function (error) {
                    console.error('Error uploading image:', error);
                    if (error.response && error.response.data) {
                        console.error('Server error details:', error.response.data);
                    }
                });
            }
        });

        function createFolderOnServer(node, parentNodeId) {
            axios.post(' {{ route("create.library") }}', {
                parent_id: parentNodeId,
                folder_name: node.text,
            })
            .then(function (response) {
                console.log(response.data);
            })
            .catch(function (error) {
                console.error('Error creating folder:', error);
            });
        }

        function editFolderOnServer(folderId, newName) {    
            axios.post('{{ route("editFolder.library") }}', {
                folder_id: folderId,
                new_name: newName,
            })
            .then(function (response) {
                console.log(response.data);
            })
            .catch(function (error) {
                console.error('Error editing folder:', error);
            });
        }

        function deleteFolderOnServer(folderId) {
            console.log('idss:', folderId);
            axios.get('{{ route("deleteFolders.library") }}', { 
                params: {
                    id: folderId,
                }
            })
            .then(function (response) {
                console.log(response.data);
            })
            .catch(function (error) {
                console.error('Error deleting folder:', error);
            });
        }

        fetchFolders();
    });
</script>

