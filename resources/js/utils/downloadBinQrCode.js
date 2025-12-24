import QRCode from 'qrcode'
import JSZip from 'jszip';
import { saveAs } from 'file-saver';

export const generateQRCodeCanvas = async (binData) => {
	let qrValue = '';
	if (binData.qrcode) {
		qrValue = binData.qrcode;
	} else {
		qrValue = binData.code
		// const baseUrl = window.location.origin;
		// const route = '/api/v1/bin-management/get/' + binData.id;
		// const fullUrl = baseUrl + route;
		// qrValue = JSON.stringify(fullUrl);
	}

	const line1 = binData.type.name;
	const line2 = binData.address;
	const fontSize1 = 20;
	const fontSize2 = 14;
	const fontFamily = "Arial";
	const qrSize = 200;

	const paddingTop = 20;
	const paddingBottom = 20;
	const paddingSides = 20;
	const paddingBetweenQRAndText = 10;
	const lineSpacing = 6;
	const lineGapBetweenLine1And2 = 10;

	// Create QR code canvas
	const qrCanvas = document.createElement("canvas");
	qrCanvas.width = qrSize;
	qrCanvas.height = qrSize;

	await QRCode.toCanvas(qrCanvas, qrValue, {
		width: qrSize,
		margin: 0,
		errorCorrectionLevel: 'H'
	});

	// Text wrapping helper
	const tempCanvas = document.createElement("canvas");
	const tempCtx = tempCanvas.getContext("2d");
	const maxTextWidth = qrCanvas.width;

	function wrapText(text, maxWidth, fontSize) {
		const ctx = tempCtx;
		ctx.font = `${fontSize}px ${fontFamily}`;
		const words = text.split(" ");
		const lines = [];
		let currentLine = "";

		for (let word of words) {
			const testLine = currentLine + word + " ";
			const testWidth = ctx.measureText(testLine).width;

			if (testWidth > maxWidth && currentLine !== "") {
				lines.push(currentLine.trim());
				currentLine = word + " ";
			} else {
				currentLine = testLine;
			}
		}
		if (currentLine) lines.push(currentLine.trim());
		return lines;
	}

	const wrappedLine1 = wrapText(line1, maxTextWidth, fontSize1);
	const wrappedLine2 = wrapText(line2, maxTextWidth, fontSize2);

	const line1Height = wrappedLine1.length * (fontSize1 + lineSpacing);
	const line2Height = wrappedLine2.length * (fontSize2 + lineSpacing);
	const totalTextHeight = line1Height + lineGapBetweenLine1And2 + line2Height;

	const combinedCanvas = document.createElement("canvas");
	combinedCanvas.width = qrCanvas.width + paddingSides * 2;
	combinedCanvas.height = paddingTop + qrCanvas.height + paddingBetweenQRAndText + totalTextHeight + paddingBottom;

	const ctx = combinedCanvas.getContext("2d");
	ctx.fillStyle = "#FFFFFF";
	ctx.fillRect(0, 0, combinedCanvas.width, combinedCanvas.height);

	// Draw QR Code
	ctx.drawImage(qrCanvas, paddingSides, paddingTop);

	// Draw text
	ctx.font = `${fontSize1}px ${fontFamily}`;
	ctx.fillStyle = "#000000";
	ctx.textAlign = "center";
	const startY = paddingTop + qrCanvas.height + paddingBetweenQRAndText;

	wrappedLine1.forEach((line, index) => {
		const y = startY + (index + 1) * (fontSize1 + lineSpacing);
		ctx.fillText(line, combinedCanvas.width / 2, y);
	});

	ctx.font = `${fontSize2}px ${fontFamily}`;
	const line2StartY = startY + line1Height + lineGapBetweenLine1And2;

	wrappedLine2.forEach((line, index) => {
		const y = line2StartY + (index + 1) * (fontSize2 + lineSpacing);
		ctx.fillText(line, combinedCanvas.width / 2, y);
	});

	return combinedCanvas;
};

export const downloadQR = async (binData) => {
	const canvas = await generateQRCodeCanvas(binData);
	const link = document.createElement("a");
	link.href = canvas.toDataURL("image/png");
	link.download = `#${binData.code}_qrcode.png`;
	link.click();
};

export const generateZipQR = async (bins) => {
    const zip = new JSZip();

    for (const bin of bins) {
		const canvas = await generateQRCodeCanvas(bin);

        const dataUrl = canvas.toDataURL("image/png");
        const base64Data = dataUrl.split(',')[1];

        zip.file(`${bin.code}_qrcode.png`, base64Data, { base64: true });
    }

    const blob = await zip.generateAsync({ type: "blob" });
    saveAs(blob, "qr_codes.zip");
};
