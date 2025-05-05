const worker = new Worker('../../assets/ai/worker.js?version=' + new Date().getTime());
const rootFolderInput = document.getElementById('rootFolder');
const startTrainingButton = document.getElementById('startTraining');
const downloadModelButton = document.getElementById('downloadModel');
const modelNameInput = document.getElementById('modelNameInput');
const modelSelect = document.getElementById('modelSelect');
const modelStatus = document.getElementById('modelStatus');
const trainingStatus = document.getElementById('trainingStatus');
const epochInput = document.getElementById('epochInput');
const batchSizeInput = document.getElementById('batchSizeInput');
const classList = document.getElementById('classList');
const classNamesDisplay = document.getElementById('classNames');
const cancelTrainingButton = document.getElementById('cancelTraining');

const progressBar = document.getElementById('progressBar');
const progressBarContainer = document.getElementById('progressBarContainer');
const trainingProgressText = document.getElementById('trainingProgressText');

let dataset = {};

document.getElementById('loadModel').addEventListener('click', async () => {
    const modelName = modelSelect.value;
    if (!modelName) {
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a model.",
        });
        return;
    }

    modelStatus.textContent = `Loading model: ${modelName}`;
    const metadataUrl = `../../machine-learning/${modelName}/metadata.json?version=${new Date().getTime()}`;

    try {
        const metadata = await fetch(metadataUrl).then(res => res.json());
        classList.innerHTML = '';
        metadata.labels.forEach(label => {
            const li = document.createElement('li');
            li.textContent = label;
            classList.appendChild(li);
        });
        modelStatus.textContent = 'Model loaded successfully.';
    } catch (error) {
        console.error("Error loading model metadata:", error);
        modelStatus.textContent = 'Failed to load model metadata.';
    }
});

rootFolderInput.addEventListener('change', (event) => {
    const files = event.target.files;
    dataset = {};

    Array.from(files).forEach((file) => {
        const folderName = file.webkitRelativePath.split('/')[1];
        if (!dataset[folderName]) dataset[folderName] = [];
        dataset[folderName].push(file);
    });

    classNamesDisplay.textContent = `Classnames: {${Object.keys(dataset).join(', ')}}`;
});

startTrainingButton.addEventListener('click', () => {
    if (Object.keys(dataset).length === 0) {
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a dataset folder.",
        });
        return;
    }

    progressBar.style.width = '0%';
    progressBarContainer.style.display = 'block'; 
    trainingProgressText.textContent = 'Training started...';

    console.log("Sending training metadata to worker...");
    worker.postMessage({
        action: 'startTraining',
        dataset: dataset,
        modelUrl: `../../machine-learning/${modelSelect.value}/model.json`,
        epochs: parseInt(epochInput.value),
        batchSize: parseInt(batchSizeInput.value),
    });
});

cancelTrainingButton.addEventListener('click', () => {
    console.log("Sending message to worker to cancel training and reloading page...");
    worker.postMessage({ action: 'cancelTraining' });
    window.location.reload();
});

worker.onmessage = (event) => {
    const { message, progress } = event.data;

    if (progress !== undefined) {
        progressBar.style.width = `${progress}%`;
        trainingProgressText.textContent = `Training Progress: ${progress.toFixed(2)}%`;
    } else {
        trainingProgressText.textContent = message;
    }

    if (message === 'Training complete and model saved successfully.') {
        progressBar.style.width = '100%';
        trainingProgressText.textContent = 'Training Completed!';
    }

    if (message === 'Training cancelled.') {
        progressBar.style.width = '0%';
        trainingProgressText.textContent = 'Training Cancelled.';
        progressBarContainer.style.display = 'none'; 
    }
};

downloadModelButton.addEventListener('click', async () => {
    const folderName = modelNameInput.value.trim() || 'retrained-model';

    if (!folderName) {
        swal({
            icon: "error",
            title: "Error",
            text: "Please enter a folder name.",
        });
        return;
    }

    try {
        const model = await tf.loadLayersModel('indexeddb://retrained-model');
        console.log("Model loaded from IndexedDB.");

        const zip = new JSZip();

        const artifacts = await model.save(tf.io.withSaveHandler(async (artifacts) => artifacts));

        const modelJson = {
            modelTopology: artifacts.modelTopology,
            weightsManifest: [{ paths: ["weights.bin"], weights: artifacts.weightSpecs }]
        };
        zip.file(`${folderName}/model.json`, JSON.stringify(modelJson));

        zip.file(`${folderName}/weights.bin`, artifacts.weightData);

        const labels = Object.keys(dataset);
        const metadata = { labels };
        zip.file(`${folderName}/metadata.json`, JSON.stringify(metadata, null, 2));

        zip.generateAsync({ type: "blob" }).then((content) => {
            const url = URL.createObjectURL(content);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${folderName}.zip`;
            link.click();
            URL.revokeObjectURL(url);

            tf.io.removeModel('indexeddb://retrained-model');
            swal({
                icon: "success",
                title: "Success",
                text: "Model files downloaded successfully in a zip file.",
            });
        });
    } catch (error) {
        console.error("Error downloading model:", error);
        swal({
            icon: "error",
            title: "Error",
            text: "Failed to download model files.",
        });
    }
});


/* =========================OLD - 10/24
const worker = new Worker('../../assets/ai/worker.js?version=' + new Date().getTime());
const rootFolderInput = document.getElementById('rootFolder');
const startTrainingButton = document.getElementById('startTraining');
const downloadModelButton = document.getElementById('downloadModel');
const modelNameInput = document.getElementById('modelNameInput');
const modelSelect = document.getElementById('modelSelect');
const modelStatus = document.getElementById('modelStatus');
const trainingStatus = document.getElementById('trainingStatus');
const epochInput = document.getElementById('epochInput');
const batchSizeInput = document.getElementById('batchSizeInput');
const classList = document.getElementById('classList');
const classNamesDisplay = document.getElementById('classNames');
const cancelTrainingButton = document.getElementById('cancelTraining');


// progress bar
const progressBar = document.getElementById('progressBar');
const progressBarContainer = document.getElementById('progressBarContainer');
const trainingProgressText = document.getElementById('trainingProgressText');

let dataset = {};


document.getElementById('loadModel').addEventListener('click', async () => {
    const modelName = modelSelect.value;
    if (!modelName) {
        tf.io.removeModel('indexeddb://retrained-model')
        //alert("Please select a model.");
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a model.",
        });
        return;
    }

    modelStatus.textContent = `Loading model: ${modelName}`;
    const metadataUrl = `../../machine-learning/${modelName}/metadata.json?version=${new Date().getTime()}`;

    //fetch or ajax is valid for fetching metadata.json
    try {
        // gets each classnames
        const metadata = await fetch(metadataUrl).then(res => res.json());
        classList.innerHTML = '';
        metadata.labels.forEach(label => {
            const li = document.createElement('li');
            li.textContent = label;
            classList.appendChild(li);
        });
        modelStatus.textContent = 'Model loaded successfully.';
        
    } catch (error) {
        console.error("Error loading model metadata:", error);
        modelStatus.textContent = 'Failed to load model metadata.';
    }
});


// user uploads the root folder, fetches the sub folder and their name for classes
rootFolderInput.addEventListener('change', (event) => {
    const files = event.target.files;
    dataset = {};

    Array.from(files).forEach((file) => {
        const folderName = file.webkitRelativePath.split('/')[1];
        if (!dataset[folderName]) dataset[folderName] = [];
        dataset[folderName].push(file);
    });

    classNamesDisplay.textContent = `Classnames: {${Object.keys(dataset).join(', ')}}`;
    //trainingStatus.textContent = `Loaded dataset with ${Object.keys(dataset).length} classes.`;
});




//start retraining >>NEW


// start training >>OLDD

startTrainingButton.addEventListener('click', () => {
    if (Object.keys(dataset).length === 0) {
        //alert('Please select a dataset folder.');
        swal({
            icon: "error",
            title: "Error",
            text: "Please select a dataset folder.",
        });
        return;
    }

    //progress bar
    progressBar.style.width = '0%';
    progressBarContainer.style.display = 'block'; 
    trainingProgressText.textContent = 'Training started...';

    console.log("Sending message to worker to start training...");
    worker.postMessage({
        action: 'startTraining',
        dataset,
        modelUrl: `../../machine-learning/${modelSelect.value}/model.json`,
        epochs: parseInt(epochInput.value),
        batchSize: parseInt(batchSizeInput.value)
    });
});


// cancel training
cancelTrainingButton.addEventListener('click', () => {
    console.log("Sending message to worker to cancel training and reloading page...");
    worker.postMessage({ action: 'cancelTraining' });  
    window.location.reload(); 
});




// worker message for loader
worker.onmessage = (event) => {
    const { message, progress } = event.data;

    if (progress !== undefined) {
        progressBar.style.width = `${progress}%`;
        trainingProgressText.textContent = `Training Progress: ${progress.toFixed(2)}%`;
    } else {
        trainingProgressText.textContent = message;
    }
    if (message === 'Training complete and model saved successfully.') {
        progressBar.style.width = '100%';
        trainingProgressText.textContent = 'Training Completed!';
    }

    if (message === 'Training cancelled.') {
        progressBar.style.width = '0%';
        trainingProgressText.textContent = 'Training Cancelled.';
        progressBarContainer.style.display = 'none'; 
    }
};





//download model
downloadModelButton.addEventListener('click', async () => {
    const folderName = modelNameInput.value.trim() || 'retrained-model';

    if (!folderName) {
        //alert("Please enter a folder name.");
        swal({
            icon: "error",
            title: "Error",
            text: "Please enter a folder name.",
        });
        return;
    }

    try {
        const model = await tf.loadLayersModel('indexeddb://retrained-model');
        console.log("Model loaded from IndexedDB.");

        const zip = new JSZip();

        // save in zip
        const artifacts = await model.save(tf.io.withSaveHandler(async (artifacts) => artifacts));

        // model.json
        const modelJson = {
            modelTopology: artifacts.modelTopology,
            weightsManifest: [{ paths: ["weights.bin"], weights: artifacts.weightSpecs }]
        };
        zip.file(`${folderName}/model.json`, JSON.stringify(modelJson));

        // weights.bin
        zip.file(`${folderName}/weights.bin`, artifacts.weightData);

        // metadata.json
        const labels = Object.keys(dataset);  
        const metadata = { labels };
        zip.file(`${folderName}/metadata.json`, JSON.stringify(metadata, null, 2));

        // generate zip file
        zip.generateAsync({ type: "blob" })
           .then(function (content) {
               const url = URL.createObjectURL(content);
               const link = document.createElement('a');
               link.href = url;
               link.download = `${folderName}.zip`;
               link.click();
               URL.revokeObjectURL(url);
               console.log("Zip file downloaded.");

               // clear the model from IndexedDB after download
               tf.io.removeModel('indexeddb://retrained-model');
               console.log("Model cleared from IndexedDB.");
               //alert('Model files downloaded successfully in a zip file.');
               swal({
                icon: "success",
                title: "Success",
                text: "Model files downloaded successfully in a zip file.",
               });
           });
    } catch ( error ) {
        console.error("Error downloading model:", error);
        //alert('Failed to download model files.');
        swal({
            icon: "error",
            title: "Error",
            text: "Failed to download model files.",
           });
    }
});






*/




