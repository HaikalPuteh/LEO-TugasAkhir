import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

let renderer, scene, camera, controls, earth, sun, time;

////// ---- Shaders ---- ////////

const EARHRADIUS = 1.0;
const atmosphere = {
    Kr: 0.0025,
    Km: 0.0010,
    ESun: 20.0, //recnet change 20-->17
    g: -0.950,
    innerRadius: EARHRADIUS,
    outerRadius: 1.025*EARHRADIUS,
    wavelength: [0.650, 0.570, 0.475],
    scaleDepth: 0.25,
    mieScaleDepth: 0.1
};

const AtmUniforms = {
    v3LightPosition: {type: "v3",value: new THREE.Vector3(1, 0, 0).normalize()},
    cPs: {type: "v3",value: new THREE.Vector3(1, 0, 0)},
    v3InvWavelength: {type: "v3",value: new THREE.Vector3(1 / Math.pow(atmosphere.wavelength[0], 4), 1 / Math.pow(atmosphere.wavelength[1], 4), 1 / Math.pow(atmosphere.wavelength[2], 4))},
    fCameraHeight: {type: "f",value: 0},
    fCameraHeight2: {type: "f",value: 0},
    fInnerRadius: {type: "f",value: atmosphere.innerRadius},
    fInnerRadius2: {type: "f",value: atmosphere.innerRadius * atmosphere.innerRadius},
    fOuterRadius: {type: "f",value: atmosphere.outerRadius},
    fOuterRadius2: {type: "f",value: atmosphere.outerRadius * atmosphere.outerRadius},
    fKrESun: {type: "f",value: atmosphere.Kr * atmosphere.ESun},
    fKmESun: {type: "f",value: atmosphere.Km * atmosphere.ESun},
    fKr4PI: {type: "f",value: atmosphere.Kr * 4.0 * Math.PI},
    fKm4PI: {type: "f",value: atmosphere.Km * 4.0 * Math.PI},
    fScale: {type: "f",value: 1 / (atmosphere.outerRadius - atmosphere.innerRadius)},
    fScaleDepth: {type: "f",value: atmosphere.scaleDepth},
    fScaleOverScaleDepth: {type: "f",value: 1 / (atmosphere.outerRadius - atmosphere.innerRadius) / atmosphere.scaleDepth},
    g: {type: "f",value: atmosphere.g},
    g2: {type: "f",value: atmosphere.g * atmosphere.g},
    nSamples: {type: "i",value: 3},
    fSamples: {type: "f",value: 3.0},
    tDisplacement: {type: "t",value: 0},
    tSkyboxDiffuse: {type: "t",value: 0},
    fNightScale: {type: "f",value: 1},
    tDiffuse: {type: "t",value: null},
    tDiffuseNight: {type: "t",value: null}
};

const vertexSky =`
// Referenced Atmospheric scattering vertex shader
//
// From author: Sean O'Neil
//
// Copyright (c) 2004 Sean O'Neil
//
//
// Edited by Chris Bolig for threejs

uniform vec3 v3LightPosition;    // The direction vector to the light source
uniform vec3 v3InvWavelength;  // 1 / pow(wavelength, 4) for the red, green, and blue channels
uniform vec3 cPs;  // camera that will rotate
uniform float fCameraHeight;   // The camera's current height
uniform float fCameraHeight2;   // fCameraHeight^2
uniform float fOuterRadius;     // The outer (atmosphere) radius
uniform float fOuterRadius2;  // fOuterRadius^2
uniform float fInnerRadius;      // The inner (planetary) radius
uniform float fInnerRadius2;   // fInnerRadius^2
uniform float fKrESun;           // Kr * ESun
uniform float fKmESun;            // Km * ESun
uniform float fKr4PI;         // Kr * 4 * PI
uniform float fKm4PI;           // Km * 4 * PI
uniform float fScale;           // 1 / (fOuterRadius - fInnerRadius)
uniform float fScaleDepth;        // The scale depth (i.e. the altitude at which the atmosphere's average density is found)
uniform float fScaleOverScaleDepth;  // fScale / fScaleDepth
const int nSamples = 3;
const float fSamples = 3.0;
varying vec3 v3Direction;
varying vec3 c0;
varying vec3 c1;
float scale(float fCos)
{
    float x = 1.0 - fCos;
    return fScaleDepth * exp(-0.00287 + x*(0.459 + x*(3.83 + x*(-6.80 + x*5.25))));
}
void main(void)
{
    float fCameraHeight = length(cPs);
    float fCameraHeight2 = fCameraHeight*fCameraHeight;
    // Get the ray from the camera to the vertex and its length (which is the far point of the ray passing through the atmosphere)
    vec3 v3Ray = position - cPs;
    float fFar = length(v3Ray);
    v3Ray /= fFar;

    // Calculate the closest intersection of the ray with the outer atmosphere (which is the near point of the ray passing through the atmosphere)
    float B = 2.0 * dot(cPs, v3Ray);
    float C = fCameraHeight2 - fOuterRadius2;
    float fDet = max(0.0, B*B - 4.0 * C);
    float fNear = 0.5 * (-B - sqrt(fDet));

    // Calculate the ray's starting position, then calculate its scattering offset
    vec3 v3Start = cPs + v3Ray * fNear;
    fFar -= fNear;
    float fStartAngle = dot(v3Ray, v3Start) / fOuterRadius;
    float fStartDepth = exp(-1.0 / fScaleDepth);
    float fStartOffset = fStartDepth * scale(fStartAngle);

    // Initialize the scattering loop variables
    float fSampleLength = fFar / fSamples;
    float fScaledLength = fSampleLength * fScale;
    vec3 v3SampleRay = v3Ray * fSampleLength;
    vec3 v3SamplePoint = v3Start + v3SampleRay * 0.5;

    // Now loop through the sample rays
    vec3 v3FrontColor = vec3(0.0, 0.0, 0.0);
    for(int i=0; i<nSamples; i++)
    {
        float fHeight = length(v3SamplePoint);
        float fDepth = exp(fScaleOverScaleDepth * (fInnerRadius - fHeight));
        float fLightAngle = dot(v3LightPosition, v3SamplePoint) / fHeight;
        float fCameraAngle = dot(v3Ray, v3SamplePoint) / fHeight;
        float fScatter = (fStartOffset + fDepth * (scale(fLightAngle) - scale(fCameraAngle)));
        vec3 v3Attenuate = exp(-fScatter * (v3InvWavelength * fKr4PI + fKm4PI));
        v3FrontColor += v3Attenuate * (fDepth * fScaledLength);
        v3SamplePoint += v3SampleRay;
    }
    // Finally, scale the Mie and Rayleigh colors and set up the varying variables for the pixel shader
    gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
    c0 = v3FrontColor * (v3InvWavelength * fKrESun);
    c1 = v3FrontColor * fKmESun;
    v3Direction = cPs - position;
}
`

const fragmentSky = `
uniform vec3 v3LightPos;
uniform float g;
uniform float g2;
varying vec3 v3Direction;
varying vec3 c0;
varying vec3 c1;

// Calculates the Mie phase function
float getMiePhase(float fCos, float fCos2, float g, float g2)
{
    return 1.5 * ((1.0 - g2) / (2.0 + g2)) * (1.0 + fCos2) / pow(1.0 + g2 - 2.0 * g * fCos, 1.5);
}

// Calculates the Rayleigh phase function
float getRayleighPhase(float fCos2)
{
    return 0.75 + 0.75 * fCos2;
}

void main (void)
{
  float fCos = dot(v3LightPos, v3Direction) / length(v3Direction);
  float fCos2 = fCos * fCos;
  vec3 color =    getRayleighPhase(fCos2) * c0 +
  getMiePhase(fCos, fCos2, g, g2) * c1;
  gl_FragColor = vec4(color, 1.0);
  gl_FragColor.a = gl_FragColor.b;
}
`

const vertexGround =`
uniform vec3 v3LightPosition;       // The direction vector to the light source
uniform vec3 cPs;       // camera that will rotate
uniform vec3 v3InvWavelength;  // 1 / pow(wavelength, 4) for the red, green, and blue channels
uniform float fCameraHeight;   // The camera's current height
uniform float fCameraHeight2;   // fCameraHeight^2
uniform float fOuterRadius;     // The outer (atmosphere) radius
uniform float fOuterRadius2;  // fOuterRadius^2
uniform float fInnerRadius;      // The inner (planetary) radius
uniform float fInnerRadius2;   // fInnerRadius^2
uniform float fKrESun;           // Kr * ESun
uniform float fKmESun;            // Km * ESun
uniform float fKr4PI;         // Kr * 4 * PI
uniform float fKm4PI;           // Km * 4 * PI
uniform float fScale;           // 1 / (fOuterRadius - fInnerRadius)
uniform float fScaleDepth;        // The scale depth (i.e. the altitude at which the atmosphere's average density is found)
uniform float fScaleOverScaleDepth;  // fScale / fScaleDepth
uniform sampler2D tDiffuse;
varying vec3 v3Direction;
varying vec3 c0;
varying vec3 c1;
varying vec3 vNormal;
varying vec2 vUv;
const int nSamples = 3;
const float fSamples = 3.0;

float scale(float fCos)
{
    float x = 1.0 - fCos;
    return fScaleDepth * exp(-0.00287 + x*(0.459 + x*(3.83 + x*(-6.80 + x*5.25))));
}

void main(void)
{
    float fCameraHeight = length(cPs);
    float fCameraHeight2 = fCameraHeight*fCameraHeight;
    // Get the ray from the camera to the vertex and its length (which is the far point of the ray passing through the atmosphere)
    vec3 v3Ray = position - cPs;
    float fFar = length(v3Ray);
    v3Ray /= fFar;
    // Calculate the closest intersection of the ray with the outer atmosphere (which is the near point of the ray passing through the atmosphere)
    float B = 2.0 * dot(cPs, v3Ray);
    float C = fCameraHeight2 - fOuterRadius2;
    float fDet = max(0.0, B*B - 4.0 * C);
    float fNear = 0.5 * (-B - sqrt(fDet));
    // Calculate the ray's starting position, then calculate its scattering offset
    vec3 v3Start = cPs + v3Ray * fNear;
    fFar -= fNear;
    float fDepth = exp((fInnerRadius - fOuterRadius) / fScaleDepth);
    float fCameraAngle = dot(-v3Ray, position) / length(position);
    float fLightAngle = dot(v3LightPosition, position) / length(position);
    float fCameraScale = scale(fCameraAngle);
    float fLightScale = scale(fLightAngle);
    float fCameraOffset = fDepth*fCameraScale;
    float fTemp = (fLightScale + fCameraScale);
    // Initialize the scattering loop variables
    float fSampleLength = fFar / fSamples;
    float fScaledLength = fSampleLength * fScale;
    vec3 v3SampleRay = v3Ray * fSampleLength;
    vec3 v3SamplePoint = v3Start + v3SampleRay * 0.5;
    // Now loop through the sample rays
    vec3 v3FrontColor = vec3(0.0, 0.0, 0.0);
    vec3 v3Attenuate;
    for(int i=0; i<nSamples; i++)
  {
    float fHeight = length(v3SamplePoint);
    float fDepth = exp(fScaleOverScaleDepth * (fInnerRadius - fHeight));
    float fScatter = fDepth*fTemp - fCameraOffset;
    v3Attenuate = exp(-fScatter * (v3InvWavelength * fKr4PI + fKm4PI));
    v3FrontColor += v3Attenuate * (fDepth * fScaledLength);
    v3SamplePoint += v3SampleRay;
  }
    // Calculate the attenuation factor for the ground
    c0 = v3Attenuate;
    c1 = v3FrontColor * (v3InvWavelength * fKrESun + fKmESun);
    vUv = uv;
    vNormal = normal;
    gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
}
`
const fragmentGround = `
uniform float fNightScale;
uniform vec3 v3LightPosition;
uniform sampler2D tDiffuse;
uniform sampler2D tDiffuseNight;
varying vec3 c0;
varying vec3 c1;
varying vec3 vNormal;
varying vec2 vUv;
void main (void)
{
    vec3 diffuseTex = texture2D( tDiffuse, vUv ).xyz;
    vec3 diffuseNightTex = texture2D( tDiffuseNight, vUv ).xyz;
    vec3 day = .75*diffuseTex * c0; //recent change 1-->.8
    vec3 night = fNightScale * diffuseNightTex  * (1.0 - c0);
    gl_FragColor = vec4(c1, 1.0) + vec4(day + night, 1.0);
}
`
////// ---- Earth ---- ////////

class Earth3d extends THREE.Group {
    static NAME = "Earth3d";
    constructor(camera) {
        super()
        this.name = Earth3d.NAME;
        this.ground = new THREE.Mesh(
            new THREE.SphereGeometry(atmosphere.innerRadius, 500, 500),
            new THREE.ShaderMaterial({
                uniforms: AtmUniforms,
                vertexShader: vertexGround,
                fragmentShader: fragmentGround
            }));
        this.add(this.ground);
        this.sky = new THREE.Mesh(
            new THREE.SphereGeometry(atmosphere.outerRadius, 500, 500),
            new THREE.ShaderMaterial({
                uniforms: AtmUniforms,
                vertexShader: vertexSky,
                fragmentShader: fragmentSky,
                side: THREE.BackSide,
                transparent: true,
                depthWrite: false,
            }))
        this.add(this.sky);
        // this.sky.material.renderOrder = 1;
        // this.sky.material.depthWrite: false;
        this._sunvect = new THREE.Vector3(1,0,0);
        this.sunvect = new THREE.Vector3(1,0,0);
        this.ground.material.uniforms.v3LightPosition.value = this._sunvect;
        this.camera = camera;          // this is a shallow copy that will follow the controls
        // use this if earth is not at world 0,0,0
        this.parentObj = new THREE.Group();  // replace with group
        this.cameracPs = new THREE.Vector3();
        this.cameracPs.copy(this.camera.position);
        this.cameracPs.sub(this.parentObj.position);
        this.ground.material.uniforms.cPs.value = this.cameracPs;
        this.axisY = new THREE.Vector3(0,1,0);
				this.quaternionRotateBack = new THREE.Quaternion(1,1,1,1);
    }
    // use this if the earth rotates around y
    update( ) {
        this.cameracPs.copy(this.camera.position);
        this.cameracPs.sub(this.parentObj.position);
        this._sunvect.copy(this.sunvect);
        this.cameracPs.applyAxisAngle(this.axisY,-this.rotation.y);
        this._sunvect.applyAxisAngle(this.axisY,-this.rotation.y);
        this.ground.material.uniforms.cPs.value = this.cameracPs;
        this.ground.material.uniforms.v3LightPosition.value = this._sunvect;
    }
    // use this if the earth will not rotate
    updateECI( ) {
        this.cameracPs.copy(this.camera.position);
        this.cameracPs.sub(this.parentObj.position);
        this.ground.material.uniforms.cPs.value = this.cameracPs;
    }
    // use this if the earth anywhere
    updateAllRotations( ) {
        this.cameracPs.copy(this.camera.position);
        this.cameracPs.sub(this.parentObj.position);
        this._sunvect.copy(this.sunvect);
        this.quaternionRotateBack.multiplyQuaternions(this.parentObj.quaternion,this.quaternion);
        //this.quaternionRotateBack.copy(this.quaternion);
        this.quaternionRotateBack.invert();
        this.cameracPs.applyQuaternion( this.quaternionRotateBack );
        this._sunvect.applyQuaternion( this.quaternionRotateBack );
        this.ground.material.uniforms.cPs.value = this.cameracPs;
        this.ground.material.uniforms.v3LightPosition.value = this._sunvect;
    }
    setSun( sun )
    {
        this.sunvect.copy(sun);
    }
    loadTextures( texDay, texNight, maxAnisotropy = 16 ) {
      texDay.anisotropy = maxAnisotropy;
      texNight.anisotropy = maxAnisotropy;
      AtmUniforms.tDiffuse.value = texDay;
      AtmUniforms.tDiffuseNight.value = texNight;
    }
}

init();
animate();
function init() {

    // renderer
    renderer = new THREE.WebGLRenderer();
    renderer.setSize( window.innerWidth, window.innerHeight );
    renderer.setPixelRatio( window.devicePixelRatio );
    document.body.appendChild( renderer.domElement );

    // scene
    scene = new THREE.Scene();

    // camera
    camera = new THREE.PerspectiveCamera( 40, window.innerWidth / window.innerHeight, .01, 1000 );
    camera.position.set( 3, 3, 3 );

    // controls
    controls = new OrbitControls( camera, renderer.domElement );

    // earth not at world 0,0,0
    const texldr = new THREE.TextureLoader();
    const diffuse = texldr.load('http://i.imgur.com/uIhmW2d.jpg');
    const diffuseNight = texldr.load('http://i.imgur.com/SUr9tYs.gif');
	const earthOffset = new THREE.Group();
    earth = new Earth3d(camera);
    earth.loadTextures(diffuse,diffuseNight);
    earthOffset.add( earth );
    scene.add(earthOffset);
    earthOffset.position.set(.5,.5,.5);
    earth.parentObj = earthOffset;

    // sun
    sun = new THREE.Vector3(1,0,0); // must be |sun|=1
    time = 0;
    

    controls.target.copy(earth.position);
    controls.update();
    
    scene.add( new THREE.AxesHelper(2));
    earth.add( new THREE.AxesHelper(2));
}

function animate() {

    requestAnimationFrame( animate );

    time += .001;
    sun.x = Math.cos(time);
    sun.y = Math.sin(time);

    earth.setSun(sun)
    
    earth.parentObj.position.x = 1.5*Math.cos(time);
    earth.parentObj.position.z = 1.5*Math.sin(time);
    
    earth.rotation.y = time;
    earth.rotation.z = time/2;
    earth.rotation.x = -time/3;
    
    earth.parentObj.rotation.y = 2*time;
    earth.parentObj.rotation.z = 2*time/2;
    earth.parentObj.rotation.x = -2*time/3;    
    
    
    earth.updateAllRotations();

    //controls.update();

    renderer.render( scene, camera );

}
