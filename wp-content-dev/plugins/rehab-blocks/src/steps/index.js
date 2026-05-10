import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

const TEMPLATE = [
	[ 'rehab/step', { title: 'Initial assessment', body: 'A confidential call with our admissions team to understand your situation and answer questions.' } ],
	[ 'rehab/step', { title: 'Treatment plan', body: 'Our clinical team designs a personalized program based on your needs and goals.' } ],
	[ 'rehab/step', { title: 'Arrival & check-in', body: 'Complimentary airport pickup and a welcoming first day at the facility.' } ],
	[ 'rehab/step', { title: 'Active treatment', body: 'Daily therapy, medical care, wellness activities, and structured group work.' } ],
];
const ALLOWED = [ 'rehab/step' ];

function Edit( { attributes, setAttributes } ) {
	const { background, layout, heading, subheading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-steps rehab-bg-${ background } rehab-steps--${ layout }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Steps settings', 'rehab-blocks' ) } initialOpen>
					<SelectControl
						label={ __( 'Layout', 'rehab-blocks' ) }
						value={ layout }
						options={ [
							{ label: 'Horizontal', value: 'horizontal' },
							{ label: 'Vertical', value: 'vertical' },
						] }
						onChange={ ( v ) => setAttributes( { layout: v } ) }
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
					<header className="rehab-steps__header">
						<RichText
							tagName="h2"
							className="rehab-heading rehab-heading--lg"
							value={ heading }
							onChange={ ( v ) => setAttributes( { heading: v } ) }
							placeholder={ __( 'Section heading…', 'rehab-blocks' ) }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
						/>
						<RichText
							tagName="p"
							className="rehab-steps__subheading"
							value={ subheading }
							onChange={ ( v ) => setAttributes( { subheading: v } ) }
							placeholder={ __( 'Optional subheading…', 'rehab-blocks' ) }
						/>
					</header>
					<ol className="rehab-steps__list">
						<InnerBlocks
							template={ TEMPLATE }
							allowedBlocks={ ALLOWED }
							renderAppender={ InnerBlocks.ButtonBlockAppender }
						/>
					</ol>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, layout, heading, subheading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-steps rehab-bg-${ background } rehab-steps--${ layout }`,
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container">
				{ ( heading || subheading ) && (
					<header className="rehab-steps__header">
						{ heading && (
							<RichText.Content
								tagName="h2"
								className="rehab-heading rehab-heading--lg"
								value={ heading }
							/>
						) }
						{ subheading && (
							<RichText.Content
								tagName="p"
								className="rehab-steps__subheading"
								value={ subheading }
							/>
						) }
					</header>
				) }
				<ol className="rehab-steps__list">
					<InnerBlocks.Content />
				</ol>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
