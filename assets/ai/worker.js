importScripts('https://cdn.jsdelivr.net/npm/@tensorflow/tfjs');

(async () => {
    try {
        if (tf.getBackend() !== 'webgl') {
            await tf.setBackend('webgl');
            await tf.ready();
            console.log("WebGL backend initialized.");
        }
    } catch (error) {
        console.error("Failed to initialize WebGL backend, switching to CPU backend:", error);
        await tf.setBackend('cpu');
        await tf.ready();
        console.log("CPU backend initialized.");
    }
})();

self.onmessage = async (event) => {
    const { action, dataset, modelUrl, epochs, batchSize } = event.data;

    if (action === 'startTraining') {
        isCancelled = false;
        try {
            console.log("Loading model for training...");
            const model = await tf.loadLayersModel(modelUrl);
            const classes = Object.keys(dataset);
            const numClasses = classes.length;

            if (model.layers[model.layers.length - 1].units !== numClasses) {
                model.pop();
                model.add(tf.layers.dense({ units: numClasses, activation: 'softmax' }));
            }

            model.summary();  

            model.layers.slice(0, -1).forEach(layer => (layer.trainable = false));

            const optimizer = tf.train.adam(0.0001);  
            model.compile({
                optimizer: optimizer,
                loss: 'categoricalCrossentropy',
                metrics: ['accuracy'],
            });

            let totalImages = 0;
            Object.values(dataset).forEach(files => (totalImages += files.length));
            const totalBatchesInEpoch = Math.ceil(totalImages / batchSize);  
            const totalBatches = totalBatchesInEpoch * epochs;  

            let completedBatches = 0;  

            for (let epoch = 0; epoch < epochs; epoch++) {
                if (isCancelled) {
                    self.postMessage({ message: 'Training cancelled.' });
                    return;
                }

                console.log(`Epoch ${epoch + 1}/${epochs} started...`);

                const dataGen = prepareDatasetGenerator(dataset, numClasses, batchSize)();

                for await (const { xs, ys } of dataGen) {
                    const logs = await model.fit(xs, ys, {
                        epochs: 1,
                        batchSize: batchSize,
                        callbacks: {
                            onBatchEnd: (batch, logs) => {
                                completedBatches++;

                                const totalProgress = (completedBatches / totalBatches) * 100;
                                self.postMessage({
                                    message: `Epoch ${epoch + 1}/${epochs}, Batch ${completedBatches % totalBatchesInEpoch + 1}/${totalBatchesInEpoch} completed. Loss: ${logs.loss.toFixed(4)}, Accuracy: ${logs.acc.toFixed(4)}`,
                                    progress: Math.min(totalProgress, 100)
                                });
                            },
                        },
                    });

                    xs.dispose();
                    ys.dispose();
                }

                console.log(`Epoch ${epoch + 1} completed.`);
            }

            await model.save('indexeddb://retrained-model');
            self.postMessage({ message: 'Training complete and model saved successfully.' });

            model.dispose();
        } catch (error) {
            console.error("Error during training:", error);
            self.postMessage({ message: `Error during training: ${error.message}` });
        }
    } else if (action === 'cancelTraining') {
        isCancelled = true;
    }
};


function shuffleArray(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]]; 
    }
}

function prepareDatasetGenerator(dataset, numClasses, batchSize) {
    const classes = Object.keys(dataset);

    return async function* () {
        const allImages = [];
        for (let classIndex = 0; classIndex < classes.length; classIndex++) {
            const className = classes[classIndex];
            const images = dataset[className];
            images.forEach(image => {
                allImages.push({ image, classIndex });
            });
        }

        shuffleArray(allImages);

        for (let i = 0; i < allImages.length; i += batchSize) {
            const batchImages = allImages.slice(i, i + batchSize);

            const tensors = [];
            const labels = [];
            for (let { image, classIndex } of batchImages) {
                const tensor = await loadImageAsTensor(image);
                tensors.push(tensor.expandDims(0));
                labels.push(classIndex);
            }

            const xs = tf.concat(tensors);
            const labelsTensor = tf.tensor1d(labels, 'int32');
            const ys = tf.oneHot(labelsTensor, numClasses);

            labelsTensor.dispose();
            tensors.forEach((tensor) => tensor.dispose());

            yield { xs, ys };
        }
    };
}

async function loadImageAsTensor(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = async (event) => {
            try {
                const response = await fetch(event.target.result);
                const blob = await response.blob();
                const imgBitmap = await createImageBitmap(blob);

                const canvas = new OffscreenCanvas(224, 224);
                const ctx = canvas.getContext('2d');
                ctx.drawImage(imgBitmap, 0, 0, 224, 224);

                const imageTensor = tf.browser.fromPixels(canvas).toFloat().div(255);
                resolve(imageTensor);
            } catch (error) {
                reject(error);
            }
        };
        reader.onerror = (error) => reject(error);
        reader.readAsDataURL(file);
    });
}

