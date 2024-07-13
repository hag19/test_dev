package main

import (
    "fmt"
    "log"
    "net/http"
)

func main() {
    // Define the port to listen on
    port := "8090"

    // Define HTTP routes
    http.HandleFunc("/", helloHandler)
    http.HandleFunc("/api/data", dataHandler)

    // Start the HTTP server
    fmt.Printf("Server is running on http://localhost:%s\n", port)
    log.Fatal(http.ListenAndServe(":"+port, nil))
}

func helloHandler(w http.ResponseWriter, r *http.Request) {
    fmt.Fprintf(w, "Hello, World!\n")
}

func dataHandler(w http.ResponseWriter, r *http.Request) {
    fmt.Fprintf(w, "API Endpoint: /api/data")
}

