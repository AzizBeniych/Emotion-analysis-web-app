#!/usr/bin/env python
# coding: utf-8

import cv2
import numpy as np
import matplotlib.pyplot as plt
import sys
from sklearn.metrics import classification_report, confusion_matrix
from keras.models import load_model

print("Starting script execution...")
# Load the saved model
print("Loading model...")
model = load_model('model_optimal.h5')

image_size = 48
emotion_mapping_inv = {0: 'Angry', 1: 'Disgust', 2: 'Fear', 3: 'Happy', 4: 'Sad', 5: 'Surprise', 6: 'Neutral'}

def preprocess_face(face):
    resized_face = cv2.resize(face, (image_size, image_size))
    gray_face = cv2.cvtColor(resized_face, cv2.COLOR_BGR2GRAY)
    normalized_face = gray_face / 255.0
    return normalized_face

def load_and_preprocess(image_path):
    image = cv2.imread(image_path)
    if image is None:
        raise ValueError("Unable to load the input image.")
    gray_image = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    return image, gray_image

def process_and_save_results(input_image, gray_image, output_path):
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')
    detected_faces = face_cascade.detectMultiScale(gray_image, scaleFactor=1.1, minNeighbors=5, minSize=(30, 30))
    processed_image = input_image.copy()
    emotions = []

    for (x, y, w, h) in detected_faces:
        face = input_image[y:y+h, x:x+w]
        preprocessed_face = preprocess_face(face)
        emotion_prediction = model.predict(preprocessed_face.reshape(1, image_size, image_size, 1))
        emotions.append(np.argmax(emotion_prediction))
        emotion_label = emotion_mapping_inv[np.argmax(emotion_prediction)]
        cv2.rectangle(processed_image, (x, y), (x+w, y+h), (0, 255, 0), 2)
        cv2.putText(processed_image, emotion_label, (x, y-10), cv2.FONT_HERSHEY_SIMPLEX, 0.9, (0, 255, 0), 2)

    # Save the processed image
    cv2.imwrite(f'{output_path}/processed_image.jpg', processed_image)
    
    # Pie chart - emotion distribution
    plt.figure(figsize=(6, 6))
    labels = [emotion_mapping_inv[i] for i in set(emotions)]
    sizes = [emotions.count(i) for i in set(emotions)]
    colors = plt.cm.Paired(range(len(labels)))
    plt.pie(sizes, labels=labels, autopct='%1.1f%%', startangle=140, colors=colors)
    plt.savefig(f'{output_path}/emotion_distribution.png')

    # Histogram - emotion frequency
    plt.figure(figsize=(10, 6))
    plt.title('Histogram of Predicted Emotions')
    plt.hist(emotions, bins=range(7), align='left', rwidth=0.8, color='skyblue', edgecolor='black')
    plt.xlabel('Emotion')
    plt.ylabel('Frequency')
    plt.xticks(range(7), [emotion_mapping_inv[i] for i in range(7)], rotation=45)
    plt.savefig(f'{output_path}/emotion_histogram.png')

    # Bar chart - face sizes
    face_sizes = [w * h for (_, _, w, h) in detected_faces]
    plt.figure(figsize=(10, 6))
    plt.bar(range(len(face_sizes)), face_sizes, color='orange')
    plt.title('Face Sizes Distribution')
    plt.xlabel('Detected Faces')
    plt.ylabel('Face Size (width * height)')
    plt.savefig(f'{output_path}/face_sizes_distribution.png')

    # Bar chart - average emotion probability
    average_probs = [np.max(model.predict(preprocess_face(input_image[y:y+h, x:x+w]).reshape(1, image_size, image_size, 1))) for (x, y, w, h) in detected_faces]
    plt.figure(figsize=(10, 6))
    plt.bar(range(len(average_probs)), average_probs, color='green')
    plt.title('Average Emotion Probability per Face')
    plt.xlabel('Detected Faces')
    plt.ylabel('Average Probability')
    plt.savefig(f'{output_path}/emotion_probability.png')

    # Correlation matrix - emotions
    emotions_one_hot = np.eye(len(emotion_mapping_inv))[emotions]
    correlation_matrix = np.corrcoef(emotions_one_hot.T)
    plt.figure(figsize=(8, 8))
    plt.imshow(correlation_matrix, cmap='coolwarm', interpolation='none')
    plt.colorbar()
    plt.xticks(range(len(emotion_mapping_inv)), list(emotion_mapping_inv.values()), rotation=45)
    plt.yticks(range(len(emotion_mapping_inv)), list(emotion_mapping_inv.values()))
    plt.title('Emotion Correlation Matrix')
    plt.savefig(f'{output_path}/emotion_correlation_matrix.png')

def main():
    print("In main function...")
    if len(sys.argv) != 3:
        print("Usage: python Sentimeter-Algorithm.py <image_path> <output_path>")
        sys.exit(1)

    image_path = sys.argv[1]
    output_path = sys.argv[2]
    print(f"Image path: {image_path}")
    print(f"Output path: {output_path}")

    try :
        input_image, gray_image = load_and_preprocess(image_path)
        process_and_save_results(input_image, gray_image, output_path)
        print("Processing and saving results completed.")
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    main()