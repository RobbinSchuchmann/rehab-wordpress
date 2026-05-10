import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextareaControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

function Edit( { attributes, setAttributes } ) {
	const { background, heading, address, embedUrl } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-map rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Map settings', 'rehab-blocks' ) } initialOpen>
					<TextareaControl
						label={ __( 'Google Maps embed URL', 'rehab-blocks' ) }
						value={ embedUrl }
						onChange={ ( v ) => setAttributes( { embedUrl: v } ) }
						help={ __( 'From Google Maps → Share → Embed a map → copy the iframe src.', 'rehab-blocks' ) }
					/>
					<SelectControl
						label={ __( 'Background', 'rehab-blocks' ) }
						value={ background }
						options={ [
							{ label: 'White', value: 'white' },
							{ label: 'Cream', value: 'cream' },
							{ label: 'Sage mist', value: 'sage-mist' },
						] }
						onChange={ ( v ) => setAttributes( { background: v } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<section { ...blockProps }>
				<div className="rehab-container">
					<div className="rehab-map__grid">
						<div className="rehab-map__info">
							<RichText
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
								onChange={ ( v ) => setAttributes( { heading: v } ) }
								placeholder={ __( 'Heading…', 'rehab-blocks' ) }
								allowedFormats={ [ 'core/bold', 'core/italic' ] }
							/>
							<RichText
								tagName="p"
								className="rehab-map__address"
								value={ address }
								onChange={ ( v ) => setAttributes( { address: v } ) }
								placeholder={ __( 'Address / details…', 'rehab-blocks' ) }
							/>
						</div>
						<div className="rehab-map__embed">
							{ embedUrl ? (
								<iframe
									src={ embedUrl }
									title={ heading || 'Location map' }
									loading="lazy"
									referrerPolicy="no-referrer-when-downgrade"
									allowFullScreen
								/>
							) : (
								<div className="rehab-map__placeholder">
									{ __( 'Add a Google Maps embed URL in the sidebar.', 'rehab-blocks' ) }
								</div>
							) }
						</div>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, heading, address, embedUrl } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-map rehab-bg-${ background }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				<div className="rehab-map__grid">
					<div className="rehab-map__info">
						{ heading && (
							<RichText.Content
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
							/>
						) }
						{ address && (
							<RichText.Content
								tagName="p"
								className="rehab-map__address"
								value={ address }
							/>
						) }
					</div>
					{ embedUrl && (
						<div className="rehab-map__embed">
							<iframe
								src={ embedUrl }
								title={ heading || 'Location map' }
								loading="lazy"
								referrerPolicy="no-referrer-when-downgrade"
								allowFullScreen
							/>
						</div>
					) }
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
