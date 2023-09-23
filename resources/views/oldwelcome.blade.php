<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Babylon.js -->
        <script src="https://cdn.babylonjs.com/babylon.js"></script>
        <script src="https://cdn.babylonjs.com/materialsLibrary/babylonjs.materials.min.js"></script>
        <script src="https://cdn.babylonjs.com/proceduralTexturesLibrary/babylonjs.proceduralTextures.min.js"></script>
        <script src="https://cdn.babylonjs.com/postProcessesLibrary/babylonjs.postProcess.min.js"></script>
        <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.js"></script>
        <script src="https://cdn.babylonjs.com/serializers/babylonjs.serializers.min.js"></script>
        <script src="https://cdn.babylonjs.com/gui/babylon.gui.min.js"></script>
        <script src="https://cdn.babylonjs.com/inspector/babylon.inspector.bundle.js"></script>
        <style>
            html, body {
                overflow: hidden;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }

            #renderCanvas {
                width: 100%;
                height: 100%;
                touch-action: none;
            }
            
            #canvasZone {
                width: 100%;
                height: 90%;
            }
        </style>
    </head>
    <body>
        <div id="canvasZone"><canvas id="renderCanvas"></canvas></div>
        <script>
            var canvas = document.getElementById("renderCanvas");

            var startRenderLoop = function (engine, canvas) {
                engine.runRenderLoop(function () {
                    if (sceneToRender && sceneToRender.activeCamera) {
                        sceneToRender.render();
                    }
                });
            }

            var engine = null;
            var scene = null;
            var sceneToRender = null;
            var createDefaultEngine = function() { return new BABYLON.Engine(canvas, true, { preserveDrawingBuffer: true, stencil: true,  disableWebGL2Support: false}); };
            var createScene = function () {
                // This creates a basic Babylon Scene object (non-mesh)
                var scene = new BABYLON.Scene(engine);
                
                var camera = new BABYLON.ArcRotateCamera("camera", BABYLON.Tools.ToRadians(90), BABYLON.Tools.ToRadians(65), 10, BABYLON.Vector3.Zero(), scene);
                //var camera = new BABYLON.ArcRotateCamera("Camera", 3 * Math.PI / 2, Math.PI / 8, 50, BABYLON.Vector3.Zero(), scene);

                // This targets the camera to scene origin
                camera.setTarget(BABYLON.Vector3.Zero());

                // This attaches the camera to the canvas
                camera.attachControl(canvas, true);
                camera.lowerRadiusLimit = 6;
                camera.upperRadiusLimit = 20;
            
                camera.useBouncingBehavior = true;
                

                // This creates a light, aiming 0,1,0 - to the sky (non-mesh)
                var light = new BABYLON.HemisphericLight("light", new BABYLON.Vector3(0, 1, 0), scene);

                // Default intensity is 1. Let's dim the light a small amount
                light.intensity = 1;

                // Append glTF model to scene.
                BABYLON.SceneLoader.Append("", "ER-2.glb", scene, function (scene) { 
                    
                    // Create a default arc rotate camera and light.
                    scene.createDefaultCameraOrLight(true, true, true);
                    scene.clearColor = new BABYLON.Color4(0, 0, 0, .2);
                    console.log(scene.camera)
                    // scene.lights[0].dispose();
                    // var light = new BABYLON.DirectionalLight("light1", new BABYLON.Vector3(-2, -3, 1), scene);
                    // light.position = new BABYLON.Vector3(6, 9, 3);
                    // var generator = new BABYLON.ShadowGenerator(512, light);
                    // generator.useBlurExponentialShadowMap = true;
                    // generator.blurKernel = 32;

                    // for (var i = 0; i < scene.meshes.length; i++) {
                    //     generator.addShadowCaster(scene.meshes[i]);    
                    // }

                    let envSet = scene.createDefaultEnvironment({
                        createGround: true,
                        createSkybox: false,
                        enableGroundMirror: true,
                    groundMirrorSizeRatio: 0.2
                    });
                    envSet.environmentTexture = null;
                    envSet.ground.dispose();
                    //envSet.setMainColor(new BABYLON.Color3(1, 1, 1, 0));
                    
                    // The default camera looks at the back of the asset.
                    // Rotate the camera by 180 degrees to the front of the asset.
                    //scene.activeCamera.alpha += Math.PI;
                    scene.activeCamera.inputs.removeByType("ArcRotateCameraMouseWheelInput");
                    

                    //scene.getMeshByName("Plane").isVisible = false;
                                        
                    // scene.meshes[1].setEnabled(false);
                    scene.meshes[1].material.albedoColor = BABYLON.Color3.Red();
                    scene.meshes[3].material.albedoColor = BABYLON.Color3.Green();
                    scene.meshes[16].material.albedoColor = BABYLON.Color3.White(); 

                    // for (i = 0; i < scene.meshes.length; i++){
                    //     scene.meshes[0].scaling = new BABYLON.Vector3(1, 1, 1);
                    // }
                });

                // BABYLON.SceneLoader.ImportMesh("","https://raw.githubusercontent.com/jewalleria/babylon/main/","necklace.glb",
                /*BABYLON.SceneLoader.ImportMesh("","/","necklace.glb",
                    scene,
                    function (meshes) {          
                        scene.createDefaultCameraOrLight(true, true, true);
                        scene.createDefaultEnvironment();
                        // The default camera looks at the back of the asset.
                        // Rotate the camera by 180 degrees to the front of the asset.
                        scene.activeCamera.alpha += Math.PI;
                });*/
                

                 // Move the light with the camera
                scene.registerBeforeRender(function () {
                    light.position = camera.position;
                });

                return scene;
            };
            
            window.initFunction = async function() {
                                
                var asyncEngineCreation = async function() {
                    try {
                    return createDefaultEngine();
                    } catch(e) {
                    console.log("the available createEngine function failed. Creating the default engine instead");
                    return createDefaultEngine();
                    }
                }

                window.engine = await asyncEngineCreation();
                if (!engine) throw 'engine should not be null.';
                startRenderLoop(engine, canvas);
                window.scene = createScene();
            };
            
            initFunction().then(() => {sceneToRender = scene                    
            });

            // Resize
            window.addEventListener("resize", function () {
                engine.resize();
            });
        </script>
    </body>
</html>

