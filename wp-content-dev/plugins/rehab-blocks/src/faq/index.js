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
	[ 'rehab/faq-item', { question: 'Is treatment confidential?', answer: 'Yes — we maintain strict privacy protocols and never disclose client information to third parties.' } ],
	[ 'rehab/faq-item', { question: 'How long is the program?', answer: 'Programs range from 4 to 12+ weeks depending on individual needs and treatment plan.' } ],
	[ 'rehab/faq-item', { question: 'Do you offer aftercare?', answer: 'Yes — every client receives a comprehensive aftercare plan with ongoing support after they leave.' } ],
];
const ALLOWED = [ 'rehab/faq-item' ];

function Edit( { attributes, setAttributes } ) {
	const { background, heading } = attributes;
	const blockProps = useBlockProps( {
		className: `rehab-faq rehab-bg-${ background }`,
	} );
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'FAQ settings', 'rehab-blocks' ) } initialOpen>
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
				<div className="rehab-container rehab-container--narrow">
					<RichText
						tagName="h2"
						className="rehab-heading rehab-heading--lg rehab-faq__heading"
						value={ heading }
						onChange={ ( v ) => setAttributes( { heading: v } ) }
						placeholder={ __( 'FAQ heading…', 'rehab-blocks' ) }
						allowedFormats={ [ 'core/bold', 'core/italic' ] }
					/>
					<div className="rehab-faq__list">
						<InnerBlocks
							template={ TEMPLATE }
							allowedBlocks={ ALLOWED }
							renderAppender={ InnerBlocks.ButtonBlockAppender }
						/>
					</div>
				</div>
			</section>
		</>
	);
}

function save( { attributes } ) {
	const { background, heading } = attributes;
	const blockProps = useBlockProps.save( {
		className: `rehab-faq rehab-bg-${ background }`,
		'aria-label': 'Frequently Asked Questions',
	} );
	return (
		<section { ...blockProps }>
			<div className="rehab-container rehab-container--narrow">
				{ heading && (
					<RichText.Content
						tagName="h2"
						className="rehab-heading rehab-heading--lg rehab-faq__heading"
						value={ heading }
					/>
				) }
				<div className="rehab-faq__list">
					<InnerBlocks.Content />
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save } );
