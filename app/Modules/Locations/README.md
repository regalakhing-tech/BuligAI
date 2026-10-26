# Locations module

**Status: structure prepared; feature implementation pending.**

Owns public office records, map pins, coordinates, address validation, and
directions links. Use Leaflet/OpenStreetMap when implementing the map.

Keep request handlers in `Controllers`, location rules in `Services`,
persistence in `Repositories`, coordinate validation in `Requests`, and map
templates in `Views`. Store public office locations rather than private applicant
addresses. See [module conventions](../../../docs/FILE_MANAGEMENT.md).
