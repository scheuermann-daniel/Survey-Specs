args <- commandArgs(TRUE)
 
## Input Simulation parameters

inputCounts<-args[1]   ## number of beds
inputLabels<-args[2]   ## number of repetitions

counts<-your_list = lapply(strsplit(inputCounts, ','), as.numeric)[[1]]
lbls<-your_list = lapply(strsplit(inputLabels, ','), as.numeric)[[1]]

png(filename="../charts/temp.png", width = 800, height = 600)
pie(counts, labels = lbls, main="Pie Chart")
dev.off()