FROM postgres:15-bullseye

COPY /docker/postgres/initdb.d/ /docker-entrypoint-initdb.d/

HEALTHCHECK --interval=10s --start-period=10s --retries=5 --timeout=5s CMD pg_isready -d postgres

USER postgres