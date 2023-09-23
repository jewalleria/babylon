<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Babylon.js -->
        <script src="https://cdn.babylonjs.com/babylon.js"></script>
        <!-- <script src="https://cdn.babylonjs.com/materialsLibrary/babylonjs.materials.min.js"></script> -->
        <!-- <script src="https://cdn.babylonjs.com/proceduralTexturesLibrary/babylonjs.proceduralTextures.min.js"></script> -->
        <!-- <script src="https://cdn.babylonjs.com/postProcessesLibrary/babylonjs.postProcess.min.js"></script> -->
        <script src="https://cdn.babylonjs.com/loaders/babylonjs.loaders.js"></script>
        <!-- <script src="https://cdn.babylonjs.com/serializers/babylonjs.serializers.min.js"></script> -->
        <!-- <script src="https://cdn.babylonjs.com/gui/babylon.gui.min.js"></script> -->
        <!-- <script src="https://cdn.babylonjs.com/inspector/babylon.inspector.bundle.js"></script> -->
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
        <button type="button" id="stonecolor">Bread</button>
        <button type="button" id="metalcolor">Tomato</button>
        <button type="button" id="screeshot">Screenshot</button>
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

            var createScene = function (canvas) {

                // This creates a basic Babylon Scene object (non-mesh)
                var scene = new BABYLON.Scene(engine);

                // Append glTF model to scene.
                BABYLON.SceneLoader.Append("", "ER-2.glb", scene,
                    function(canvas) {
                        //Set Camera
                        const camera = scene.createDefaultCamera(true, true, true);
                        scene.clearColor = new BABYLON.Color4(0, 0, 0, .2);
                        scene.activeCamera.attachControl(canvas, true);
                        //scene.activeCamera.lowerRadiusLimit = scene.activeCamera.radius;
                        //scene.activeCamera.upperRadiusLimit = scene.activeCamera.radius;
                        // scene.activeCamera.alpha += Math.PI;

                        //Set Lighting
                        const lighting = BABYLON.CubeTexture.CreateFromPrefilteredData("studio.env", scene);
                        lighting.name = "studioCam";
                        lighting.gammaSpace = false;
                        scene.environmentTexture = lighting;

                        //Create Diamond Material
                        var pbr = new BABYLON.PBRMaterial("diamond", scene);
                        pbr.metallic = .3;
                        pbr.roughness = 0;
                        pbr.alpha = .75;
                        pbr.iridescence = true;
                        pbr.subSurface._isScatteringEnabled = true;
                        pbr.albedoTexture = new BABYLON.Texture("{{url('diamondtexture.jpg')}}");
                        pbr._emissiveColor = BABYLON.Color3.FromHexString('#FFC000').toLinearSpace();
                        pbr._emissiveIntensity = .2;
                        

                        scene.meshes[1].material = pbr;
                        scene.meshes[2].material = pbr;
                        scene.meshes[3].material = pbr;
                        scene.meshes[4].material = pbr;
                        scene.meshes[5].material = pbr;
                        scene.meshes[6].material = pbr;
                        scene.meshes[7].material = pbr;
                        scene.meshes[8].material = pbr;
                        scene.meshes[1].material.albedoColor = BABYLON.Color3.FromHexString('#FFC000').toLinearSpace();
                        
                        for (i = 0; i < scene.meshes.length; i++){
                            console.log(scene.meshes[i]);
                        }

                    },
                    function() {
                        console.log("### Loading 3d models")
                    },
                    function(error) {
                        console.error("### Error loading model")
                        console.error(error)
                    }
                );
                          
                
                var stonecolor = document.getElementById('stonecolor');
                var metalcolor = document.getElementById('metalcolor');
                stonecolor.onclick = function() {
                    //change the bread color
                    const topBread = scene.getMeshById('bred up.001_primitive0');
                    topBread.material.albedoColor = BABYLON.Color3.Black();
                };

                metalcolor.onclick = function() {
                    //change the cheese color
                    const bottomBread = scene.getMeshesByID('tomato.004'); console.log('bottomBread',bottomBread)
                    bottomBread.forEach(mesh => {
                       mesh.material.albedoColor = BABYLON.Color3.Blue();
                    });
                };

                //add screenshot button
                var ssButton = document.getElementById("screeshot");
                ssButton.onclick = function () {
                    //scene.activeCamera.alpha += 1;
                    scene.activeCamera.beta += .5;
                    // BABYLON.Tools.CreateScreenshot(engine, scene.activeCamera, { width: 1024, height: 300 },
                    // function (data) {
                    //     var img = document.createElement("img");
                    //     img.src = data;
                    //     document.body.appendChild(img);
                    // });
                    
                };	

                return scene;

            }


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

