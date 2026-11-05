/**
 * Small planar-geometry helpers for corridor sweeps (metres, lat/lng pairs as [lat, lng]).
 * Accuracy is plenty for ~10 km corridors; no external deps.
 */

const EARTH_R = 6371000;
const DEG = Math.PI / 180;

export function haversineMeters(a, b) {
  const dLat = (b[0] - a[0]) * DEG;
  const dLng = (b[1] - a[1]) * DEG;
  const s =
    Math.sin(dLat / 2) ** 2 +
    Math.cos(a[0] * DEG) * Math.cos(b[0] * DEG) * Math.sin(dLng / 2) ** 2;
  return 2 * EARTH_R * Math.asin(Math.sqrt(s));
}

/** Local equirectangular projection around a reference latitude → metres. */
export function makeProjector(refLat) {
  const k = Math.cos(refLat * DEG);
  return {
    toXY: ([lat, lng]) => [lng * DEG * EARTH_R * k, lat * DEG * EARTH_R],
    toLatLng: ([x, y]) => [y / (DEG * EARTH_R), x / (DEG * EARTH_R * k)],
  };
}

export function polylineLengthMeters(line) {
  let len = 0;
  for (let i = 1; i < line.length; i++) len += haversineMeters(line[i - 1], line[i]);
  return len;
}

export function polylineCentroid(line) {
  const n = line.length;
  const lat = line.reduce((s, p) => s + p[0], 0) / n;
  const lng = line.reduce((s, p) => s + p[1], 0) / n;
  return { lat, lng };
}

/**
 * Distance from a point to a polyline, plus position along the line (metres from the first vertex).
 * @returns {{ distance: number, position: number }}
 */
export function pointToPolyline(point, line) {
  const { toXY } = makeProjector(point[0]);
  const [px, py] = toXY(point);
  let best = { distance: Infinity, position: 0 };
  let cursor = 0;
  for (let i = 1; i < line.length; i++) {
    const [ax, ay] = toXY(line[i - 1]);
    const [bx, by] = toXY(line[i]);
    const segLen2 = (bx - ax) ** 2 + (by - ay) ** 2;
    const segLen = Math.sqrt(segLen2);
    let t = segLen2 ? ((px - ax) * (bx - ax) + (py - ay) * (by - ay)) / segLen2 : 0;
    t = Math.max(0, Math.min(1, t));
    const qx = ax + t * (bx - ax);
    const qy = ay + t * (by - ay);
    const d = Math.hypot(px - qx, py - qy);
    if (d < best.distance) best = { distance: d, position: cursor + t * segLen };
    cursor += segLen;
  }
  return best;
}

/**
 * Circle centres along a polyline every `stepMeters`, including both ends.
 * @returns {Array<{lat:number,lng:number}>}
 */
export function tilesAlongPolyline(line, stepMeters) {
  const out = [];
  if (!line.length) return out;
  const push = (p) => out.push({ lat: +p[0].toFixed(6), lng: +p[1].toFixed(6) });
  push(line[0]);
  let carry = 0; // distance since last tile
  for (let i = 1; i < line.length; i++) {
    const a = line[i - 1];
    const b = line[i];
    const segLen = haversineMeters(a, b);
    if (segLen === 0) continue;
    let along = stepMeters - carry;
    while (along <= segLen) {
      const t = along / segLen;
      push([a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t]);
      along += stepMeters;
    }
    carry = segLen - (along - stepMeters);
  }
  const last = line[line.length - 1];
  const lastTile = out[out.length - 1];
  if (haversineMeters([lastTile.lat, lastTile.lng], last) > stepMeters / 2) push(last);
  return out;
}

/**
 * Four child circles that together cover the parent circle.
 * Offsets ±r/2 on each axis with radius 0.72r (worst case point at (r,0) is 0.707r from a child centre).
 */
export function subdivideCircle(center, radiusMeters, factor = 0.72) {
  const { toXY, toLatLng } = makeProjector(center.lat);
  const [cx, cy] = toXY([center.lat, center.lng]);
  const o = radiusMeters / 2;
  const childRadius = Math.round(radiusMeters * factor);
  return [
    [cx - o, cy - o],
    [cx + o, cy - o],
    [cx - o, cy + o],
    [cx + o, cy + o],
  ].map(([x, y]) => {
    const [lat, lng] = toLatLng([x, y]);
    return { lat: +lat.toFixed(6), lng: +lng.toFixed(6), radius: childRadius };
  });
}

/** Does the circle intersect the corridor buffer at all? Used to drop child tiles that fall outside. */
export function circleTouchesCorridor(center, radiusMeters, line, bufferMeters) {
  const { distance } = pointToPolyline([center.lat, center.lng], line);
  return distance <= radiusMeters + bufferMeters;
}
