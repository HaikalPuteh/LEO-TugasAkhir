import * as THREE from "three";

/**
 * Creates a combined day/night terminator + Fresnel glow material.
 * @param {{ rimHex?: number, facingHex?: number, sunDirection?: THREE.Vector3 }} options
 * @returns {THREE.ShaderMaterial}
 */

function glowmesh({ rimHex = 0x0088ff, facingHex = 0xE0F0FF, sunDirection = new THREE.Vector3(0, 0, 1) } = {}) {
  const uniforms = {
    color1:           { value: new THREE.Color(rimHex) },
    color2:           { value: new THREE.Color(facingHex) },
    fresnelBias:      { value: 0.6 },
    fresnelScale:     { value: 1.0 },
    fresnelPower:     { value: 0.01 },
    // Day/night terminator controls:
    termScale:        { value: 7.0 },
    termOffset:       { value: 0.1 },
    uSunDirection:    { value: sunDirection }, // Now accepts sunDirection as an argument
    uCameraPosition:  { value: new THREE.Vector3() }
  };

  const vs = `
    varying vec3 vWorldNormal;
    varying vec3 vWorldPosition;

    void main() {
      vWorldNormal   = normalize(mat3(modelMatrix) * normal);
      vWorldPosition = (modelMatrix * vec4(position, 1.0)).xyz;
      gl_Position    = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
    }
  `;

  const fs = `
    uniform vec3 color1;
    uniform vec3 color2;
    uniform vec3 uSunDirection;
    uniform float fresnelBias;
    uniform float fresnelScale;
    uniform float fresnelPower;
    uniform float termScale;
    uniform float termOffset;
    uniform vec3 uCameraPosition;

    varying vec3 vWorldNormal;
    varying vec3 vWorldPosition;

    void main() {
      // 1) Fresnel factor
      vec3 viewDir = normalize(uCameraPosition - vWorldPosition);
      float f_raw = fresnelBias + fresnelScale * pow(
        1.0 + dot(viewDir, vWorldNormal),
        fresnelPower
      );
      float f = clamp(f_raw, 0.0, 1.0);

      // 2) Day/night terminator mask (logistic)
      float cosSun = dot(vWorldNormal, normalize(uSunDirection));
      float term = 1.0 / (1.0 + exp(-termScale * (cosSun + termOffset)));

      // 3) Sun-relative rim fade
      float fCombined = f * term;

      // 4) Combined color & alpha
      vec3  color = mix(color2, color1, fCombined);
      float alpha = fCombined; // full fade based on sunlit Fresnel

      gl_FragColor = vec4(color, alpha);
    }
  `;

  return new THREE.ShaderMaterial({
    uniforms,
    vertexShader:   vs,
    fragmentShader: fs,
    transparent:    true,
    blending:       THREE.AdditiveBlending,
    side:           THREE.FrontSide, 
    depthWrite:     false
  });
}

export { glowmesh };
