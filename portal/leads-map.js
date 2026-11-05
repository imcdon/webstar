(function () {
  const el = document.getElementById("lead-map-data");
  const mapEl = document.getElementById("lead-map");
  const listEl = document.getElementById("lead-map-list");
  const countEl = document.getElementById("lead-map-count");
  if (!el || !mapEl) return;

  let data;
  try {
    data = JSON.parse(el.textContent || "{}");
  } catch {
    return;
  }

  const GRADE_COLOR = {
    none: "#e11d48",
    F: "#ea580c",
    D: "#d97706",
    C: "#ca8a04",
    "?": "#64748b",
    B: "#0d9488",
    A: "#16a34a",
  };

  const map = L.map(mapEl, { scrollWheelZoom: true });
  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
  }).addTo(map);

  const bounds = L.latLngBounds([]);
  (data.corridors || []).forEach(function (c) {
    if (c.bufferPolygon && c.bufferPolygon.length) {
      const poly = L.polygon(c.bufferPolygon, {
        color: "#0f766e",
        weight: 1,
        fillColor: "#14b8a6",
        fillOpacity: 0.12,
        interactive: false,
      }).addTo(map);
      bounds.extend(poly.getBounds());
    }
    if (c.polyline && c.polyline.length) {
      const line = L.polyline(c.polyline, {
        color: "#0f766e",
        weight: 3,
        opacity: 0.9,
        interactive: false,
      }).addTo(map);
      bounds.extend(line.getBounds());
    }
  });

  const cluster = L.markerClusterGroup({
    showCoverageOnHover: false,
    maxClusterRadius: 48,
    spiderfyOnMaxZoom: true,
  });
  map.addLayer(cluster);

  const markersById = new Map();

  function markerFor(lead) {
    const color = GRADE_COLOR[lead.grade] || "#64748b";
    const m = L.circleMarker([lead.lat, lead.lng], {
      radius: 8,
      color: "#04191a",
      weight: 1,
      fillColor: color,
      fillOpacity: 0.95,
    });
    const site = lead.website
      ? `<p><a href="${escapeAttr(lead.website)}" target="_blank" rel="noopener noreferrer">${escapeHtml(lead.website)}</a></p>`
      : "<p>No website on Google listing</p>";
    const maps = lead.maps
      ? `<p><a href="${escapeAttr(lead.maps)}" target="_blank" rel="noopener noreferrer">Google Maps listing</a></p>`
      : "";
    m.bindPopup(
      `<div class="lead-map-popup">
        <strong>${escapeHtml(lead.name || "Unnamed")}</strong>
        <p>Grade <b>${escapeHtml(lead.grade)}</b> · need ${escapeHtml(lead.need)} · ${lead.distance != null ? lead.distance + " m from road" : ""}</p>
        <p>${escapeHtml(lead.pain || lead.type || "")}</p>
        ${site}${maps}
      </div>`,
      { maxWidth: 280 }
    );
    m.leadId = lead.id;
    markersById.set(lead.id, m);
    return m;
  }

  function selectedNeeds() {
    return new Set(
      Array.from(document.querySelectorAll('.lead-map-filters input[name="need"]:checked')).map(function (i) {
        return i.value;
      })
    );
  }

  function visibleLeads() {
    const needs = selectedNeeds();
    return (data.leads || []).filter(function (l) {
      return needs.has(l.need);
    });
  }

  function render() {
    cluster.clearLayers();
    const vis = visibleLeads();
    vis.forEach(function (lead) {
      cluster.addLayer(markerFor(lead));
    });
    if (countEl) {
      countEl.textContent = vis.length + " shown of " + (data.leads || []).length;
    }
    if (listEl) {
      listEl.innerHTML = vis
        .map(function (lead) {
          return (
            `<li><button type="button" class="lead-map-list__btn" data-id="${escapeAttr(lead.id)}">` +
            `<span class="lead-map-swatch lead-map-swatch--${gradeClass(lead.grade)}"></span>` +
            `<span class="lead-map-list__name">${escapeHtml(lead.name || "Unnamed")}</span>` +
            `<span class="lead-map-list__grade">${escapeHtml(lead.grade)}</span>` +
            `</button></li>`
          );
        })
        .join("");
    }
  }

  function gradeClass(g) {
    return g === "?" ? "q" : g;
  }

  listEl &&
    listEl.addEventListener("click", function (e) {
      const btn = e.target.closest("[data-id]");
      if (!btn) return;
      const m = markersById.get(btn.getAttribute("data-id"));
      if (!m) return;
      map.setView(m.getLatLng(), Math.max(map.getZoom(), 17), { animate: true });
      cluster.zoomToShowLayer(m, function () {
        m.openPopup();
      });
    });

  document.querySelectorAll('.lead-map-filters input[name="need"]').forEach(function (input) {
    input.addEventListener("change", render);
  });

  if (bounds.isValid()) {
    map.fitBounds(bounds, { padding: [24, 24] });
  } else {
    map.setView([42.69, -83.38], 13);
  }
  render();
  setTimeout(function () {
    map.invalidateSize();
  }, 200);

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }
  function escapeAttr(s) {
    return escapeHtml(s);
  }
})();
