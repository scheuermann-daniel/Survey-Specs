# my_rscript.R

# Retrieve command-line arguments
args <- commandArgs(TRUE)

# Extract values for the pie chart from command-line arguments
x <- args[1]
y <- args[2]

# Split the comma-separated values into vectors
counts <- unlist(strsplit(x, ","))
labels <- unlist(strsplit(y, ","))

# Create a PNG file for the pie chart with specified dimensions
png(filename="charts/temp.png", width=300, height=300)

# Generate a pie chart using the values and labels
pie(strtoi(counts), labels)

# Close the PNG device
dev.off()
