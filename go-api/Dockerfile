# Start with a base image that has Go installed
FROM golang:1.17-alpine

# Set the working directory inside the container
WORKDIR /app

# Copy the entire application into the container (including go.mod if it exists)
COPY . .

# If go.mod doesn't exist, initialize a new Go module with a default path
RUN test -f go.mod || go mod init github.com/hag19/go-api

# Download dependencies
RUN go mod download

# Build the Go application
RUN go build -o main .

# Expose port 8090 to the outside world
EXPOSE 8090

# Command to run the executable
CMD ["./main"]
