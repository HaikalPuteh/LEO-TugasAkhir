//getStarfield.js

import * as THREE from "three";

// ✅ Use direct path string to the public file
const circleTextureUrl = "/textures/stars/circle.png";

export default function getStarfield({ numStars = 500 } = {}) {
    function randomSpherePoint() {
        const radius = Math.random() * 25 + 25;
        const u = Math.random();
        const v = Math.random();
        const theta = 2 * Math.PI * u;
        const phi = Math.acos(2 * v - 1);
        let x = radius * Math.sin(phi) * Math.cos(theta);
        let y = radius * Math.sin(phi) * Math.sin(theta);
        let z = radius * Math.cos(phi);

        return {
            pos: new THREE.Vector3(x, y, z),
            hue: 0.6,
            minDist: radius,
        };
    }

    const verts = [];
    const colors = [];
    const positions = [];

    for (let i = 0; i < numStars; i++) {
        let p = randomSpherePoint();
        const { pos, hue } = p;
        positions.push(p);
        const col = new THREE.Color().setHSL(hue, 0.2, Math.random());
        verts.push(pos.x, pos.y, pos.z);
        colors.push(col.r, col.g, col.b);
    }

    const geo = new THREE.BufferGeometry();
    geo.setAttribute("position", new THREE.Float32BufferAttribute(verts, 3));
    geo.setAttribute("color", new THREE.Float32BufferAttribute(colors, 3));

    const texture = new THREE.TextureLoader().load(circleTextureUrl);

    const mat = new THREE.PointsMaterial({
        size: 0.2,
        vertexColors: true,
        map: texture,
        transparent: true,
        depthWrite: false
    });

    const points = new THREE.Points(geo, mat);
    return points;
}
