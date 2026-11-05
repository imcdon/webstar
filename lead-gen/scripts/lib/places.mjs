import { getGoogleKey } from "./env.mjs";

const PLACES_BASE = "https://places.googleapis.com/v1";

/** Normalize to bare id or places/ChIJ... */
export function normalizePlaceId(id) {
  if (!id) return "";
  if (id.startsWith("places/")) return id;
  return `places/${id}`;
}

export function barePlaceId(id) {
  return normalizePlaceId(id).replace(/^places\//, "");
}

async function placesPost(path, body, fieldMask, apiKey) {
  const res = await fetch(`${PLACES_BASE}${path}`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Goog-Api-Key": apiKey,
      "X-Goog-FieldMask": fieldMask,
    },
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    const msg = data.error?.message || res.statusText;
    throw new Error(`Places API ${path}: ${msg}`);
  }
  return data;
}

export async function geocodeAddress(address, apiKey = getGoogleKey()) {
  const url = new URL("https://maps.googleapis.com/maps/api/geocode/json");
  url.searchParams.set("address", address);
  url.searchParams.set("key", apiKey);
  const res = await fetch(url);
  const data = await res.json();
  if (data.status !== "OK" || !data.results?.[0]) {
    throw new Error(`Geocode failed for "${address}": ${data.status}`);
  }
  const loc = data.results[0].geometry.location;
  return { lat: loc.lat, lng: loc.lng };
}

export async function searchNearby({ center, radiusMeters, includedType, maxResultCount = 20, pageToken }) {
  const apiKey = getGoogleKey();
  const fieldMask = "places.id,places.displayName,places.googleMapsUri,places.primaryType,places.businessStatus";
  const body = {
    includedTypes: [includedType],
    maxResultCount,
    locationRestriction: {
      circle: {
        center: { latitude: center.lat, longitude: center.lng },
        radius: radiusMeters,
      },
    },
  };
  if (pageToken) body.pageToken = pageToken;
  return placesPost("/places:searchNearby", body, fieldMask, apiKey);
}

/**
 * Nearby Search with no type filter, ranked by distance, Enterprise field mask (websiteUri).
 * One call returns up to 20 places with location + website — no Place Details needed.
 */
export const NEARBY_SWEEP_FIELD_MASK = [
  "places.id",
  "places.displayName",
  "places.location",
  "places.primaryType",
  "places.types",
  "places.businessStatus",
  "places.googleMapsUri",
  "places.websiteUri",
  "places.userRatingCount",
].join(",");

export async function searchNearbyAll({ center, radiusMeters, excludedTypes = [], maxResultCount = 20 }) {
  const apiKey = getGoogleKey();
  const body = {
    maxResultCount,
    rankPreference: "DISTANCE",
    locationRestriction: {
      circle: {
        center: { latitude: center.lat, longitude: center.lng },
        radius: radiusMeters,
      },
    },
  };
  if (excludedTypes.length) body.excludedTypes = excludedTypes.slice(0, 50);
  return placesPost("/places:searchNearby", body, NEARBY_SWEEP_FIELD_MASK, apiKey);
}

export function mapSweepRow(place) {
  const base = mapPlaceRow(place);
  if (!base) return null;
  return {
    ...base,
    lat: place.location?.latitude ?? null,
    lng: place.location?.longitude ?? null,
    websiteUri: place.websiteUri?.trim() || "",
    types: Array.isArray(place.types) ? place.types : [],
    userRatingCount: Number.isFinite(place.userRatingCount) ? place.userRatingCount : null,
  };
}

export async function searchText({ textQuery, center, radiusMeters, pageToken }) {
  const apiKey = getGoogleKey();
  const fieldMask = "places.id,places.displayName,places.googleMapsUri,places.primaryType,places.businessStatus";
  const body = { textQuery, pageToken };
  if (center && radiusMeters) {
    body.locationBias = {
      circle: {
        center: { latitude: center.lat, longitude: center.lng },
        radius: radiusMeters,
      },
    };
  }
  return placesPost("/places:searchText", body, fieldMask, apiKey);
}

export async function placeDetails(placeId) {
  const apiKey = getGoogleKey();
  // Path must be /v1/places/{placeId} — do not encode the "places/" segment or Google returns Not Found.
  const bare = barePlaceId(placeId);
  const fieldMask = "id,displayName,websiteUri,googleMapsUri,primaryType,businessStatus";
  const res = await fetch(`${PLACES_BASE}/places/${encodeURIComponent(bare)}`, {
    headers: {
      "X-Goog-Api-Key": apiKey,
      "X-Goog-FieldMask": fieldMask,
    },
  });
  const data = await res.json().catch(() => ({}));
  if (!res.ok) {
    throw new Error(`Place Details: ${data.error?.message || res.statusText}`);
  }
  return data;
}

export function mapPlaceRow(place) {
  if (!place?.id) return null;
  if (place.businessStatus === "CLOSED_PERMANENTLY") return null;
  return {
    placeId: barePlaceId(place.id),
    googleMapsUri: place.googleMapsUri ?? null,
    primaryType: place.primaryType ?? null,
    displayName: place.displayName?.text ?? "",
  };
}
