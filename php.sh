#!/bin/bash
PARAMS=""
for PARAM in "$@"
do
    PARAMS="${PARAMS} \"${PARAM}\""
done
bash -c "php php.php ${PARAMS}"