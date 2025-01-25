from flask import Flask, render_template, Response, jsonify
import cv2
import tensorflow as tf
import numpy as np
import os

app = Flask(__name__)

# Check current working directory
print("Current Working Directory:", os.getcwd())

# Load the pre-trained emotion detection model
try:
    model = tf.keras.models.load_model('./model.h5')  # Ensure model is in the correct directory
    emotion_labels = ['Angry', 'Disgust', 'Fear', 'Happy', 'Neutral', 'Sad', 'Surprise']
    print("Model loaded successfully.")
except Exception as e:
    print(f"Error loading model: {e}")
    model = None

# Initialize webcam
camera = cv2.VideoCapture(0)

# Variable to store the last detected emotion
emotion_result = "Neutral"  # Default emotion if no emotion detected yet

def generate_frames():
    global emotion_result  # Reference to global emotion_result variable
    while True:
        success, frame = camera.read()
        if not success:
            print("Failed to capture image from webcam")
            break
        else:
            try:
                # Preprocess the frame
                gray_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
                resized_frame = cv2.resize(gray_frame, (48, 48))  # Resize to 48x48
                normalized_frame = resized_frame / 255.0  # Normalize pixel values
                reshaped_frame = np.expand_dims(normalized_frame, axis=-1)  # Add channel dimension
                input_frame = np.expand_dims(reshaped_frame, axis=0)  # Add batch dimension

                if model:
                    # Perform emotion prediction
                    predictions = model.predict(input_frame, verbose=0)
                    emotion_index = np.argmax(predictions)
                    emotion_result = emotion_labels[emotion_index]  # Update global emotion result

                    # Debugging: Print predictions and detected emotion
                    print(f"Predictions: {predictions}")
                    print(f"Detected Emotion: {emotion_result}")

                    # Display emotion on the frame
                    cv2.putText(frame, emotion_result, (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 2)
                else:
                    cv2.putText(frame, "Model Not Loaded", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)
            except Exception as e:
                print(f"Error during prediction: {e}")
                cv2.putText(frame, "Error in Emotion Detection", (50, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 2)

            # Encode frame to be sent to client
            _, buffer = cv2.imencode('.jpg', frame)
            frame = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame + b'\r\n')

@app.route('/')
def index():
    return render_template('feature_detection.php')  # Serve the main HTML page

@app.route('/video_feed')
def video_feed():
    return Response(generate_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/emotion')
def get_emotion():
    # Return the most recent emotion prediction as JSON
    return jsonify({'emotion': emotion_result})

if __name__ == "__main__":
    app.run(debug=True)
