


document.getElementById('loadModel').addEventListener('click', () => {
    const modelSelect = document.getElementById('modelSelect');
    const modelName = modelSelect.value;
    const classList = document.getElementById('classList');
    const modelStatus = document.getElementById('modelStatus');

    if (!modelName) {
        tf.io.removeModel('indexeddb://retrained-model');

        swal({
            icon: "error",
            title: "Error",
            text: "Please select a model",
        });
        return;
    }

    const metadataUrl = `../../machine-learning/${modelName}/metadata.json?version=${new Date().getTime()}`;

    $.ajax({
        url: metadataUrl,
        method: 'GET',
        dataType: 'json',
        success: function(metadata) {
            classList.innerHTML = '';
            metadata.labels.forEach(label => {
                const row = document.createElement('tr');
                const cell = document.createElement('td');
                cell.textContent = label;
                row.appendChild(cell);
                classList.appendChild(row);
            });
            //modelStatus.textContent = 'Model loaded successfully.';
        },
        error: function(xhr, status, error) {
            console.error("Error loading model metadata:", status, error);
            var stats = 'Failed to load model metadata.';

            swal({
                icon: "error",
                title: "Error",
                text: stats,
            });
        }
    });
});




document.getElementById('deploy_model').addEventListener('click', () =>{
    const modelSelect = document.getElementById('modelSelect');
    const modelName = modelSelect.value;

    if (!modelName) {
        tf.io.removeModel('indexeddb://retrained-model');
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a model",
        });
        return;
    }


    swal({
        icon: "warning",
        title: "Deploy Model",
        text: "This model will be used for the website. Are you sure you want to proceed?",
        dangerMode: true,
        buttons: true,
    }).then((proceeds)=>{
        if(proceeds){
            $.ajax({
                url: "/a.crud",
                method: "POST",
                data:{model_name: modelName},
                success: function(response){
                    var manageModel = response.trim();
                    switch(manageModel){
                        case "Success":
                            swal({
                                icon:"success",
                                title: manageModel,
                            })
                            

                            break;
                        case "Failed":
                            swal({
                                icon:"error",
                                title: "An error occured, please try again later. ",
                            });
                            break;
                        default:
                            swal({
                                icon:"error",
                                title: "Something went wrong, please try again later. " ,
                            });
                            break;
                    }
                },
            });
        }
    });
});



document.getElementById('delete_model').addEventListener('click', () =>{
    const modelSelect = document.getElementById('modelSelect');
    const modelName = modelSelect.value;

    if (!modelName) {
        tf.io.removeModel('indexeddb://retrained-model');
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a model",
        });
        return;
    }
    
    swal({
        icon: "warning",
        title: "Remove Model",
        text: "This model will be removed. Are you sure you want to proceed?",
        dangerMode: true,
        buttons: true,
    }).then((proceeds)=>{
        if(proceeds){
           $.ajax({
            url: "/a.crud",
            method: "POST",
            data: {deleteModel: modelName},
            success: function(response){
                var deleteModel = response.trim();
                console.log(deleteModel);
                switch(deleteModel){
                    case "Folder does not exist.":
                        swal({
                            icon:"error",
                            title: deleteModel,
                        });
                        break;
                    case "Success":
                        swal({
                            icon:"success",
                            title: "Success",
                        }).then((dones)=>{
                            window.location.reload();
                        });
                        break;
                    default:
                        swal({
                            icon:"error",
                            title: "Something went wrong, please try again later. " ,
                        });
                        break;
                }
            }

           });
        }
    });
});





$('#upload_model').on('change', function () {
    const uploadInput = $('#upload_model')[0];
    const modelNameInput = $('#modelName');
    const files = uploadInput.files;

    if (files.length > 0) {
        const folderPath = files[0].webkitRelativePath || files[0].name;
        const folderName = folderPath.split('/')[0];
        modelNameInput.val(folderName);

        let isValid = true;
        for (const file of files) {
            if (!file.name.endsWith('.json') && !file.name.endsWith('.bin')) {
                isValid = false;
                break;
            }
        }

        if (!isValid) {
            swal({
                icon: "error",
                title: "Invalid Files",
                text: "Only .json and .bin files are allowed in the folder.",
            });
            $('#upload_model').val('');
            modelNameInput.val('');
        }
    }
});


$('#uploadForm').on('submit', function (e) {
    e.preventDefault();

    const uploadInput = $('#upload_model')[0];
    const modelName = $('#modelName').val();
    const uploadStatus = $('#uploadStatus');

    if (!uploadInput.files.length) {
        swal({
            icon: "warning",
            title: "No Folder Selected",
            text: "Please select a folder before proceeding.",
        });
        return;
    }

    if (!modelName) {
        swal({
            icon: "warning",
            title: "Model Name Missing",
            text: "Please provide a model name.",
        });
        return;
    }

    swal({
        icon: "warning",
        title: "Upload Model",
        text: "Are you sure you want to upload this model?",
        dangerMode: true,
        buttons: true,
    }).then((proceed) => {
        if (proceed) {
            const formData = new FormData();
            formData.append('modelName', modelName);

            for (const file of uploadInput.files) {
                formData.append('files[]', file);
            }

            $.ajax({
                url: "/a.crud",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    uploadStatus.text('Uploading...');
                },
                success: function (response) {
                    const result = response.trim();
                    console.log(result);
                    switch (result) {
                        case "Success":
                            swal({
                                icon: "success",
                                title: "Model Uploaded Successfully!",
                            }).then((dones)=>{
                                window.location.reload();
                            });
                            
                            break;
                        case "Failed":
                            swal({
                                icon: "error",
                                title: "Upload Failed",
                                text: "An error occurred during the upload. Please try again.",
                            });
                            uploadStatus.text('Upload failed.');
                            break;
                        default:
                            swal({
                                icon: "error",
                                title: "Unexpected Error",
                                text: "Something went wrong. Please try again later.",
                            });
                            uploadStatus.text('Unexpected error.');
                            break;
                    }
                },
                error: function () {
                    swal({
                        icon: "error",
                        title: "Network Error",
                        text: "Unable to connect to the server. Please check your network connection.",
                    });
                    uploadStatus.text('Network error.');
                },
            });
        }
    });
});

$('#cancelBtn').on('click', function () {
    $('#upload_model').val('');
    $('#modelName').val('');
    $('#uploadStatus').text('Upload canceled.');
    swal({
        icon: "info",
        title: "Canceled",
        text: "The selected folder has been removed.",
    });
});


































//============================================================================================
//============================================================================================





let model;
let labels = []; 
let videoStream;
let isPredicting = false;

const videoElement = document.getElementById("camera_prev");
const modelSelect = document.getElementById("modelSelect");
const startButton = document.getElementById("startCam");
const stopButton = document.getElementById("stopCam");
const predictionTable = document.getElementById("prediction_table"); 
const predictionDelay = 1000; 

async function loadModelAndLabels() {
    const selectedModel = modelSelect.value;

    if (!selectedModel) {
        swal("Error", "Please select a model.", "error");
        return null;
    }

    const modelFolderPath = `../../machine-learning/${selectedModel}/model.json`;
    const metadataPath = `../../machine-learning/${selectedModel}/metadata.json`;

    try {
        model = await tf.loadLayersModel(modelFolderPath);
        console.log(`Model '${selectedModel}' loaded from folder.`);

        const metadata = await fetch(metadataPath).then(response => response.json());

        if (metadata.labels) {
            labels = metadata.labels;
            console.log("Labels loaded from metadata.json:", labels);
        } else {
            throw new Error("No labels found in metadata.json.");
        }
    } catch (error) {
        console.error(`Failed to load model or labels for '${selectedModel}'.`, error);
        swal("Error", "Failed to load the selected model. Please check your files.", "error");
        return null;
    }
    return model;
}

async function startCamera() {
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: { width: 250, height: 250 } });
        videoElement.srcObject = videoStream;
        await videoElement.play();
        isPredicting = true;
        startPredictionLoop();
    } catch (error) {
        console.error("Error accessing camera:", error);
        swal("Error", "Could not access the camera.", "error");
    }
}

function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
    }
    isPredicting = false;
    videoElement.srcObject = null;

    predictionTable.innerHTML = "<tr><td>No Predictions</td><td>N/A</td></tr>";
}

async function startPredictionLoop() {
    let retries = 5; 
    while (isPredicting) {
        if (videoElement.videoWidth === 0 || videoElement.videoHeight === 0) {
            if (retries-- > 0) {
                console.warn("Video feed not ready. Retrying...");
                await new Promise(resolve => setTimeout(resolve, 500));
                continue;
            }
            console.error("Video feed failed to initialize.");
            break;
        }

        const predictions = await makePrediction();
        if (predictions) {
            updateTable(predictions);
        }

        await new Promise(resolve => setTimeout(resolve, predictionDelay));
    }
}

async function makePrediction() {
    if (!model || labels.length === 0 || !videoElement.srcObject) return null;

    if (videoElement.videoWidth === 0 || videoElement.videoHeight === 0) {
        console.warn("Video element not ready for prediction.");
        return null; 
    }

    try {
        const predictions = tf.tidy(() => {
            
            const tensor = tf.browser.fromPixels(videoElement)
                .resizeNearestNeighbor([224, 224]) 
                .toFloat()
                .div(127.5)
                .sub(1)
                .expandDims();

            return model.predict(tensor);
        });

        const predictionData = await predictions.data();
        predictions.dispose(); 

        return Array.from(predictionData)
            .map((confidence, index) => ({
                label: labels[index] || "Unknown",
                confidence: confidence,
            }))
            .sort((a, b) => b.confidence - a.confidence); 
    } catch (error) {
        console.error("Error during prediction:", error);
        return null;
    }
}

function updateTable(predictions) {
    predictionTable.innerHTML = ""; 

    predictions.forEach(({ label, confidence }) => {
        const row = document.createElement("tr");
        const labelCell = document.createElement("td");
        const confidenceCell = document.createElement("td");

        labelCell.textContent = label;
        confidenceCell.textContent = `${(confidence * 100).toFixed(2)}%`;

        row.appendChild(labelCell);
        row.appendChild(confidenceCell);
        predictionTable.appendChild(row);
    });
}

startButton.addEventListener("click", async () => {
    if (!model) {
        await loadModelAndLabels(); 
    }

    if (model) {
        await startCamera();
    } else {
        swal("Error", "Please load a model first.", "error");
    }
});

stopButton.addEventListener("click", () => {
    stopCamera();
});




















const uploadInput = document.getElementById("upload_image");
const uploadPreviewContainer = document.querySelector(".upload_preview");
const predictionResultsBody = document.getElementById("upload_prediction_results"); // Unique tbody


async function loadModelAndLabels() {
    const selectedModel = modelSelect.value;

    if (!selectedModel) {
        swal("Error", "Please select a model.", "error");
        return null;
    }

    const modelPath = `../../machine-learning/${selectedModel}/model.json`;
    const metadataPath = `../../machine-learning/${selectedModel}/metadata.json`;

    try {
        model = await tf.loadLayersModel(modelPath);
        console.log(`Model '${selectedModel}' loaded.`);

        const metadata = await fetch(metadataPath).then(res => res.json());
        labels = metadata.labels || [];
        console.log("Labels loaded from metadata.json:", labels);
    } catch (error) {
        console.error("Error loading model or labels:", error);
        swal("Error", "Could not load model or labels.", "error");
    }
}

uploadInput.addEventListener("change", async (event) => {
    const file = event.target.files[0];

    if (!file) {
        swal("Error", "No file selected.", "error");
        return;
    }

    if (!model) {
        await loadModelAndLabels();
    }

    if (!model) {
        swal("Error", "Please load a model first.", "error");
        return;
    }

    const imgElement = document.createElement("img");
    imgElement.classList.add("image_preview"); 
    imgElement.src = URL.createObjectURL(file);

    imgElement.onload = async () => {
        URL.revokeObjectURL(imgElement.src); 

        const width = imgElement.naturalWidth;
        const height = imgElement.naturalHeight;

        if (width === 0 || height === 0) {
            swal("Error", "Uploaded image has invalid dimensions.", "error");
            return;
        }

        uploadPreviewContainer.innerHTML = "";
        uploadPreviewContainer.appendChild(imgElement);

        const predictions = await makePredictionFromImage(imgElement);
        if (predictions) {
            updatePredictionResults(predictions);
        }
    };

    imgElement.onerror = () => {
        swal("Error", "Failed to load the uploaded image.", "error");
    };
});


async function makePredictionFromImage(imageElement) {
    if (!model || labels.length === 0) return null;

    const predictions = tf.tidy(() => {

        const tensor = tf.browser.fromPixels(imageElement)
            .resizeNearestNeighbor([224, 224])
            .toFloat()
            .div(127.5)
            .sub(1)
            .expandDims();

        console.log("Input tensor shape:", tensor.shape);

        return model.predict(tensor);
    });

    const predictionData = await predictions.data();
    predictions.dispose(); 

    return Array.from(predictionData)
        .map((confidence, index) => ({
            label: labels[index] || "Unknown",
            confidence: confidence
        }))
        .sort((a, b) => b.confidence - a.confidence); 
}

function updatePredictionResults(predictions) {
    predictionResultsBody.innerHTML = ""; 

    predictions.forEach(({ label, confidence }) => {
        const row = document.createElement("tr");
        const labelCell = document.createElement("td");
        const confidenceCell = document.createElement("td");

        labelCell.textContent = label;
        confidenceCell.textContent = `${(confidence * 100).toFixed(2)}%`;

        row.appendChild(labelCell);
        row.appendChild(confidenceCell);
        predictionResultsBody.appendChild(row); 
    });

    console.log("Prediction results updated:", predictions);
}
