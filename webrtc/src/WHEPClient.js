import negotiateConnectionWithClientOffer from "./negotiateConnectionWithClientOffer.js";

export default class WHEPClient {
	constructor(endpoint, videoElement) {
		this.endpoint = endpoint;
		this.videoElement = videoElement;
		this.stream = new MediaStream();
	
		this.peerConnection = new RTCPeerConnection({
			iceServers: [
				{
					urls: "stun:stun.cloudflare.com:3478",
				},
			],
			bundlePolicy: "max-bundle",
		});
		/** https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/addTransceiver */
		this.peerConnection.addTransceiver("video", {
			direction: "recvonly",
		});
		this.peerConnection.addTransceiver("audio", {
			direction: "recvonly",
		});
		/**
		 * When new tracks are received in the connection, store local references,
		 * so that they can be added to a MediaStream, and to the <video> element.
		 *
		 * https://developer.mozilla.org/en-US/docs/Web/API/RTCPeerConnection/track_event
		 */
		this.peerConnection.ontrack = (event) => {
			const track = event.track;
			const currentTracks = this.stream.getTracks();
			const streamAlreadyHasVideoTrack = currentTracks.some(
				(track) => track.kind === "video"
			);
			const streamAlreadyHasAudioTrack = currentTracks.some(
				(track) => track.kind === "audio"
			);
			switch (track.kind) {
				case "video":
					if (streamAlreadyHasVideoTrack) {
						break;
					}
					this.stream.addTrack(track);
					break;
				case "audio":
					if (streamAlreadyHasAudioTrack) {
						break;
					}
					this.stream.addTrack(track);
					break;
				default:
					console.log("got unknown track " + track);
			}
		};
		this.peerConnection.addEventListener("connectionstatechange", (ev) => {
			if (this.peerConnection.connectionState !== "connected") {
				return;
			}
			if (!this.videoElement.srcObject) {
				this.videoElement.srcObject = this.stream;
			}
		});
		this.peerConnection.addEventListener("negotiationneeded", (ev) => {
			negotiateConnectionWithClientOffer(this.peerConnection, this.endpoint);
		});
	}
}
