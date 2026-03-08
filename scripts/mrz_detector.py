import cv2
import sys

try:
    image_path = sys.argv[1]
    output_path = sys.argv[2]

    print(f"Processing image: {image_path}")
    image = cv2.imread(image_path)
    if image is None:
        print(f"Error: Could not read image from {image_path}")
        sys.exit(1)

    print(f"Image shape: {image.shape}")
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)

    rectKernel = cv2.getStructuringElement(cv2.MORPH_RECT, (25,7))
    sqKernel = cv2.getStructuringElement(cv2.MORPH_RECT, (21,21))

    blackhat = cv2.morphologyEx(gray, cv2.MORPH_BLACKHAT, rectKernel)

    gradX = cv2.Sobel(blackhat, ddepth=cv2.CV_32F, dx=1, dy=0)
    gradX = cv2.convertScaleAbs(gradX)

    gradX = cv2.morphologyEx(gradX, cv2.MORPH_CLOSE, rectKernel)
    thresh = cv2.threshold(gradX,0,255,cv2.THRESH_BINARY+cv2.THRESH_OTSU)[1]

    contours,_ = cv2.findContours(thresh,cv2.RETR_EXTERNAL,cv2.CHAIN_APPROX_SIMPLE)
    print(f"Found {len(contours)} contours")

    mrz_contours = []
    for c in contours:
        x,y,w,h = cv2.boundingRect(c)
        print(f"Contour: x={x}, y={y}, w={w}, h={h}, image_width={image.shape[1]}")
        if w > image.shape[1]*0.5 and h > 25:
            mrz_contours.append((x, y, w, h))

    if mrz_contours:
        # Sort by y ascending (top to bottom)
        mrz_contours.sort(key=lambda c: c[1])
        # Get the bounding box of all MRZ contours
        min_x = min(c[0] for c in mrz_contours)
        min_y = min(c[1] for c in mrz_contours)
        max_x = max(c[0] + c[2] for c in mrz_contours)
        max_y = max(c[1] + c[3] for c in mrz_contours)
        mrz = image[min_y:max_y, min_x:max_x]
        cv2.imwrite(output_path, mrz)
        print("MRZ detection successful")
    else:
        print("Error: No MRZ detected in the image")
        sys.exit(2)

    print("MRZ detection successful")

except Exception as e:
    print(f"Error: {e}")
    sys.exit(3)
