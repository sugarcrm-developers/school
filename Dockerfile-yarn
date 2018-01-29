FROM node:6.7.0

RUN npm install -g yarn

WORKDIR /workspace

# Ensure that a volume is mounted to /workspace when this container is run
COPY entry_point.sh /opt/bin/entry_point.sh
RUN chmod +x /opt/bin/entry_point.sh
ENTRYPOINT ["/opt/bin/entry_point.sh"]

# Run bash so that the container remains running after the ENTRYPOINT script finishes
CMD ["bash"]
