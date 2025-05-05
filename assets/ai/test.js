

let model;
let labels = []; 
let videoStream;
let isPredicting = false;

const videoElement = document.getElementById("camera");
const modelSelect = document.getElementById("modelSelect");
const loadModelButton = document.getElementById("loadModel");
const startButton = document.getElementById("startPreview");
const stopButton = document.getElementById("stopPreview");
const predictionLabel = document.getElementById("predictionLabel");
const predictionConfidence = document.getElementById("predictionConfidence");
const predictionDelay = 1000; 

// load model 
async function loadModelAndLabels() {
    const selectedModel = modelSelect.value;

    if (!selectedModel) {
        alert("Please select a model.");
        return null;
    }

    const modelPathIndexedDB = `indexeddb://retrained-model`;
    const modelFolderPath = `../../machine-learning/${selectedModel}/model.json`;
    const metadataPath = `../../machine-learning/${selectedModel}/metadata.json`;

    try {
        model = await tf.loadLayersModel(modelPathIndexedDB);
        console.log(`Model '${selectedModel}' loaded from IndexedDB.`);

        // load labels 
        const metadataResponse = await fetch(`indexeddb://retrained-model/metadata.json`)
            .then(response => response.json())
            .catch(() => null);

        if (metadataResponse && metadataResponse.labels) {
            labels = metadataResponse.labels;
            console.log("Labels loaded from IndexedDB metadata:", labels);
        } else {
            throw new Error("Metadata not found in IndexedDB.");
        }
    } catch (error) {
        console.log(`Attempting to load model '${selectedModel}' and metadata from folder.`);

        try {
            model = await tf.loadLayersModel(modelFolderPath);
            console.log(`Model '${selectedModel}' loaded from folder.`);

            const metadata = await fetch(metadataPath)
                .then(response => response.json());

            if (metadata.labels) {
                labels = metadata.labels;
                console.log("Labels loaded from metadata.json:", labels);
            } else {
                throw new Error("No labels found in metadata.json.");
            }
        } catch (backupError) {
            console.error(`No model or metadata available for '${selectedModel}' in IndexedDB or folder.`, backupError);
            alert(`Failed to load model and labels for '${selectedModel}'. Please make sure both are available.`);
            return null;
        }
    }
    return model;
}

// start camera
async function startCamera() {
    try {
        videoStream = await navigator.mediaDevices.getUserMedia({ video: true });
        videoElement.srcObject = videoStream;
        await videoElement.play();
        isPredicting = true;
        startPredictionLoop();
    } catch (error) {
        console.error("Error accessing camera:", error);
        alert("Could not access the camera.");
    }
}

// stop camera
function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
    }
    isPredicting = false;
    videoElement.srcObject = null;
}

async function startPredictionLoop() {
    while (isPredicting) {
        const prediction = await makePrediction();
        if (prediction) {
            const { label, confidence } = prediction;
            predictionLabel.textContent = label;
            predictionConfidence.textContent = `${(confidence * 100).toFixed(2)}%`;
        }
        await new Promise(resolve => setTimeout(resolve, predictionDelay)); // Add delay
    }
}


async function makePrediction() {
    if (!model || labels.length === 0 || !videoElement.srcObject) return null;

    if (videoElement.videoWidth === 0 || videoElement.videoHeight === 0) {
        console.warn("Video element not ready for prediction.");
        return null;
    }


    const prediction = tf.tidy(() => {
        const tensor = tf.browser.fromPixels(videoElement)
            .resizeNearestNeighbor([224, 224])
            .toFloat()
            .div(127.5)
            .sub(1)
            .expandDims();
        // then mag predict
        return model.predict(tensor);
    });


    const predictions = await prediction.data();
    prediction.dispose(); 

    const maxConfidence = Math.max(...predictions);
    const maxIndex = predictions.indexOf(maxConfidence);

    return {
        label: labels[maxIndex] || "Unknown",
        confidence: maxConfidence
    };
}

startButton.addEventListener("click", async () => {
    if (!model) {
        await loadModelAndLabels(); 
    }

    if (model) {
        await startCamera();
    } else {
        alert("Please load a model first.");
    }
});


stopButton.addEventListener("click", () => {
    stopCamera();
    predictionLabel.textContent = "N/A";
    predictionConfidence.textContent = "0%";
});

loadModelButton.addEventListener("click", async () => {
    await loadModelAndLabels();
});
